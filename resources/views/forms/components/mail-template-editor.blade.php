@php
    $statePath = $getStatePath();
@endphp

<x-dynamic-component :component="$getFieldWrapperView()" :field="$field">
    <div
        x-data="{
            state: $wire.{{ $applyStateBindingModifiers("entangle('{$statePath}')") }},
            mode: 'visual',
            savedRange: null,
            init() {
                this.$nextTick(() => this.$refs.editor.innerHTML = this.state || '')
                this.$watch('state', value => {
                    if (this.mode === 'visual' && document.activeElement !== this.$refs.editor && this.$refs.editor.innerHTML !== (value || '')) {
                        this.$refs.editor.innerHTML = value || ''
                    }
                })
            },
            sync() { this.state = this.$refs.editor.innerHTML },
            saveSelection() {
                const selection = window.getSelection()
                if (selection.rangeCount && this.$refs.editor.contains(selection.anchorNode)) this.savedRange = selection.getRangeAt(0).cloneRange()
            },
            restoreSelection() {
                this.$refs.editor.focus()
                if (!this.savedRange) return
                const selection = window.getSelection()
                selection.removeAllRanges()
                selection.addRange(this.savedRange)
            },
            command(name, value = null) {
                this.restoreSelection()
                document.execCommand(name, false, value)
                this.sync()
                this.saveSelection()
            },
            block(tag) { this.command('formatBlock', tag) },
            insert(html) {
                this.restoreSelection()
                document.execCommand('insertHTML', false, html)
                this.sync()
            },
            addLink() {
                const url = window.prompt('Enter the link URL (https://...)')
                if (url) this.command('createLink', url)
            },
            addImage() {
                const url = window.prompt('Enter the image URL (https://...)')
                if (url) this.insert(`<img src=&quot;${url.replace(/&/g, '&amp;').replaceAll(String.fromCharCode(34), '&quot;')}&quot; alt=&quot;&quot; style=&quot;max-width:100%;height:auto;&quot;>`)
            },
            addButton() {
                const label = window.prompt('Button text', 'Read more')
                if (!label) return
                const url = window.prompt('Button URL', 'https://')
                if (url) this.insert(`<p style=&quot;text-align:center;margin:24px 0&quot;><a href=&quot;${url}&quot; style=&quot;display:inline-block;padding:12px 22px;background:#f59e0b;color:#ffffff;text-decoration:none;border-radius:6px;font-weight:700&quot;>${label}</a></p>`)
            },
            starter() {
                if (this.state && !window.confirm('Replace the current body with a starter template?')) return
                this.state = `<div style=&quot;max-width:640px;margin:0 auto;background:#ffffff;color:#1f2937;font-family:Arial,sans-serif&quot;><div style=&quot;padding:32px 24px;text-align:center;background:#111827;color:#ffffff&quot;><h1 style=&quot;margin:0;font-size:30px&quot;>Newsletter [[month]]</h1></div><div style=&quot;padding:32px 24px&quot;><h2 style=&quot;margin-top:0&quot;>Hello!</h2><p style=&quot;line-height:1.6&quot;>Add your newsletter content here.</p><p style=&quot;text-align:center;margin-top:32px&quot;><a href=&quot;https://&quot; style=&quot;display:inline-block;padding:12px 22px;background:#f59e0b;color:#ffffff;text-decoration:none;border-radius:6px;font-weight:700&quot;>Visit website</a></p></div><div style=&quot;padding:20px 24px;text-align:center;background:#f3f4f6;color:#6b7280;font-size:12px&quot;>Your footer text</div></div>`
                this.$nextTick(() => this.$refs.editor.innerHTML = this.state)
            },
            setMode(next) {
                if (this.mode === 'visual') this.sync()
                this.mode = next
                if (next === 'visual') this.$nextTick(() => this.$refs.editor.innerHTML = this.state || '')
            }
        }"
        class="overflow-hidden rounded-xl border border-gray-300 bg-white shadow-sm dark:border-white/10 dark:bg-gray-900"
    >
        <div class="flex flex-wrap items-center gap-1 border-b border-gray-200 bg-gray-50 p-2 dark:border-white/10 dark:bg-white/5" x-show="mode === 'visual'">
            @foreach ([['B', 'bold', 'Bold'], ['I', 'italic', 'Italic'], ['U', 'underline', 'Underline'], ['S', 'strikeThrough', 'Strikethrough']] as [$text, $command, $title])
                <button type="button" title="{{ $title }}" @mousedown.prevent="command('{{ $command }}')" class="rounded px-3 py-2 text-sm font-semibold hover:bg-gray-200 dark:hover:bg-white/10">{{ $text }}</button>
            @endforeach

            <span class="mx-1 h-6 border-l border-gray-300 dark:border-white/20"></span>
            <select title="Text style" @change="block($event.target.value); $event.target.selectedIndex = 0" class="rounded-lg border-0 bg-transparent py-2 pl-2 pr-7 text-sm dark:bg-gray-800">
                <option value="" selected disabled>Style</option>
                <option value="p">Paragraph</option><option value="h1">Heading 1</option><option value="h2">Heading 2</option><option value="h3">Heading 3</option><option value="blockquote">Quote</option>
            </select>
            <select title="Font size" @change="command('fontSize', $event.target.value); $event.target.selectedIndex = 0" class="rounded-lg border-0 bg-transparent py-2 pl-2 pr-7 text-sm dark:bg-gray-800">
                <option value="" selected disabled>Size</option><option value="2">Small</option><option value="3">Normal</option><option value="5">Large</option><option value="7">Extra large</option>
            </select>

            <label title="Text color" @mousedown="saveSelection()" class="flex cursor-pointer items-center gap-1 rounded px-2 py-2 text-xs hover:bg-gray-200 dark:hover:bg-white/10">A <input type="color" value="#111827" @input="command('foreColor', $event.target.value)" class="h-5 w-6 cursor-pointer border-0 bg-transparent p-0"></label>
            <label title="Highlight color" @mousedown="saveSelection()" class="flex cursor-pointer items-center gap-1 rounded px-2 py-2 text-xs hover:bg-gray-200 dark:hover:bg-white/10">Highlight <input type="color" value="#fef08a" @input="command('hiliteColor', $event.target.value)" class="h-5 w-6 cursor-pointer border-0 bg-transparent p-0"></label>

            <span class="mx-1 h-6 border-l border-gray-300 dark:border-white/20"></span>
            <button type="button" title="Align left" @mousedown.prevent="command('justifyLeft')" class="rounded px-2 py-2 hover:bg-gray-200 dark:hover:bg-white/10">≡</button>
            <button type="button" title="Align center" @mousedown.prevent="command('justifyCenter')" class="rounded px-2 py-2 hover:bg-gray-200 dark:hover:bg-white/10">≣</button>
            <button type="button" title="Align right" @mousedown.prevent="command('justifyRight')" class="rounded px-2 py-2 hover:bg-gray-200 dark:hover:bg-white/10">☰</button>
            <button type="button" title="Bulleted list" @mousedown.prevent="command('insertUnorderedList')" class="rounded px-2 py-2 hover:bg-gray-200 dark:hover:bg-white/10">• List</button>
            <button type="button" title="Numbered list" @mousedown.prevent="command('insertOrderedList')" class="rounded px-2 py-2 hover:bg-gray-200 dark:hover:bg-white/10">1. List</button>
            <button type="button" title="Link" @mousedown.prevent="saveSelection(); addLink()" class="rounded px-2 py-2 hover:bg-gray-200 dark:hover:bg-white/10">Link</button>
            <button type="button" title="Image from URL" @mousedown.prevent="saveSelection(); addImage()" class="rounded px-2 py-2 hover:bg-gray-200 dark:hover:bg-white/10">Image</button>
            <button type="button" title="Call-to-action button" @mousedown.prevent="saveSelection(); addButton()" class="rounded px-2 py-2 hover:bg-gray-200 dark:hover:bg-white/10">Button</button>
            <button type="button" title="Undo" @mousedown.prevent="command('undo')" class="rounded px-2 py-2 hover:bg-gray-200 dark:hover:bg-white/10">↶</button>
            <button type="button" title="Redo" @mousedown.prevent="command('redo')" class="rounded px-2 py-2 hover:bg-gray-200 dark:hover:bg-white/10">↷</button>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-2 border-b border-gray-200 px-3 py-2 text-sm dark:border-white/10">
            <div class="flex flex-wrap gap-2">
                @foreach (['month', 'name', 'email', 'phone', 'message', 'improve', 'reply'] as $placeholder)
                    <button type="button" @click="insert('[[{{ $placeholder }}]]')" class="rounded-md bg-gray-100 px-2 py-1 font-mono text-xs dark:bg-white/10">+ [[{{ $placeholder }}]]</button>
                @endforeach
                <button type="button" @click="starter()" class="rounded-md bg-primary-600 px-3 py-1 text-xs font-semibold text-white">Use starter template</button>
            </div>
            <div class="flex rounded-lg bg-gray-100 p-1 dark:bg-white/10">
                <button type="button" @click="setMode('visual')" :class="mode === 'visual' && 'bg-white shadow dark:bg-gray-700'" class="rounded-md px-3 py-1 text-xs">Design</button>
                <button type="button" @click="setMode('html')" :class="mode === 'html' && 'bg-white shadow dark:bg-gray-700'" class="rounded-md px-3 py-1 text-xs">HTML</button>
                <button type="button" @click="setMode('preview')" :class="mode === 'preview' && 'bg-white shadow dark:bg-gray-700'" class="rounded-md px-3 py-1 text-xs">Preview</button>
            </div>
        </div>

        <div
            x-ref="editor"
            x-show="mode === 'visual'"
            contenteditable="true"
            role="textbox"
            aria-multiline="true"
            aria-label="Mail template body"
            @input.debounce.300ms="sync()"
            @keyup="saveSelection()"
            @mouseup="saveSelection()"
            class="prose min-h-96 max-w-none p-5 text-gray-950 outline-none dark:prose-invert dark:text-white"
        ></div>
        <textarea
            x-show="mode === 'html'"
            x-model.debounce.300ms="state"
            spellcheck="false"
            aria-label="Mail template HTML"
            placeholder="Enter the email HTML here..."
            class="min-h-96 w-full resize-y border-0 p-5 font-mono text-sm outline-none"
            style="background-color: #030712; color: #86efac; caret-color: #ffffff; color-scheme: dark;"
        ></textarea>
        <div x-show="mode === 'preview'" class="min-h-96 bg-gray-100 p-5 dark:bg-gray-950">
            <div class="mx-auto max-w-3xl overflow-auto bg-white p-4 text-gray-950 shadow" x-html="state"></div>
        </div>
    </div>
</x-dynamic-component>
