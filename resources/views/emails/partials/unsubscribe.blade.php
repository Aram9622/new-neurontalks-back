<div style="margin-top:32px;padding-top:20px;border-top:1px solid #e5e7eb;text-align:center;color:#6b7280;font-family:Arial,sans-serif;font-size:12px;line-height:1.5">
    <p style="margin:0 0 12px">You are receiving this email because you subscribed to the NeuronTalks newsletter.</p>
    <p style="margin:0 0 12px">
        <a href="{{ $subscription->unsubscribeUrl() }}" style="display:inline-block;padding:10px 18px;background:#4b5563;color:#ffffff;text-decoration:none;border-radius:6px;font-weight:700">Unsubscribe</a>
    </p>
    <p style="margin:0">If the button does not work, use this link:<br><a href="{{ $subscription->unsubscribeUrl() }}" style="color:#4b5563;word-break:break-all">{{ $subscription->unsubscribeUrl() }}</a></p>
</div>
