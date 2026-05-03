<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NeuronTalks Response</title>
    <style>
        @media screen and (max-width: 600px) {
            .content-table {
                width: 100% !important;
            }
            .content-padding {
                padding: 30px 20px !important;
            }
            .header-padding {
                padding: 30px 15px !important;
            }
            .button-container {
                display: block !important;
                text-align: center !important;
                margin-top: 25px !important;
            }
            .signature-container {
                text-align: center !important;
                margin-bottom: 20px !important;
            }
        }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f1f5f9; -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%;">
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style="background-color: #f1f5f9;">
        <tr>
            <td align="center" style="padding: 20px 10px;">
                <!--[if (gte mso 9)|(IE)]>
                <table align="center" border="0" cellspacing="0" cellpadding="0" width="600">
                <tr>
                <td align="center" valign="top" width="600">
                <![endif]-->
                <table class="content-table" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width: 600px; background-color: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 12px rgba(0,0,0,0.05);">
                    
                    <!-- Header -->
                    <tr>
                        <td align="center" class="header-padding" style="background-color: #0c4a6e; padding: 45px 30px; border-bottom: 4px solid #38bdf8;">
                            <h1 style="color: #ffffff; margin: 0; font-size: 28px; font-weight: 300; letter-spacing: 1.5px; font-family: 'Georgia', serif; text-transform: uppercase;">
                                NEURON<span style="font-weight: 700; color: #38bdf8;">TALKS</span>
                            </h1>
                            <p style="color: #bae6fd; margin: 10px 0 0 0; font-size: 12px; font-style: italic; letter-spacing: 1px; opacity: 0.9;">Exploring the Boundaries of Knowledge</p>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td class="content-padding" style="padding: 45px 50px 30px 50px; font-family: 'Segoe UI', Arial, sans-serif;">
                            <p style="color: #64748b; font-size: 13px; font-weight: 700; text-transform: uppercase; margin: 0 0 12px 0; letter-spacing: 1px;">Response to Inquiry</p>
                            <h2 style="color: #0f172a; margin: 0 0 25px 0; font-size: 22px; font-weight: 600; font-family: 'Georgia', serif;">Dear {{ $contact->name }},</h2>
                            
                            <p style="color: #334155; font-size: 16px; line-height: 1.7; margin-bottom: 35px;">
                                We appreciate your engagement with <strong>NeuronTalks</strong>. Our editorial board has carefully reviewed your inquiry. Please find our response below.
                            </p>

                            <!-- Original Inquiry -->
                            <div style="background-color: #f8fafc; border-radius: 8px; padding: 25px; margin-bottom: 40px; border-left: 4px solid #0c4a6e;">
                                <p style="color: #0c4a6e; font-size: 11px; font-weight: 700; text-transform: uppercase; margin: 0 0 8px 0; letter-spacing: 0.5px;">Your Original Message:</p>
                                <p style="color: #475569; font-size: 15px; margin: 0; line-height: 1.6; font-style: italic;">
                                    "{{ $contact->message }}"
                                </p>
                            </div>

                            <!-- Official Response -->
                            <div style="margin-bottom: 30px;">
                                <p style="color: #0f172a; font-size: 18px; font-weight: 700; margin-bottom: 15px; font-family: 'Georgia', serif;">Our Response:</p>
                                <div style="color: #1e293b; font-size: 16px; line-height: 1.8; background-color: #ffffff; border: 1px solid #e2e8f0; border-radius: 8px; padding: 20px; white-space: pre-line;">
                                    {{ $replyMessage }}
                                </div>
                            </div>
                        </td>
                    </tr>

                    <!-- Signature & CTA -->
                    <tr>
                        <td class="content-padding" style="padding: 0 50px 45px 50px; font-family: 'Segoe UI', Arial, sans-serif;">
                            <table border="0" cellpadding="0" cellspacing="0" width="100%" style="border-top: 1px solid #f1f5f9; padding-top: 30px;">
                                <tr>
                                    <td class="signature-container" valign="top">
                                        <p style="color: #0f172a; font-size: 15px; font-weight: 700; margin: 0;">Sincerely,</p>
                                        <p style="color: #64748b; font-size: 14px; margin: 5px 0 0 0;">NeuronTalks Board</p>
                                    </td>
                                    <td class="button-container" align="right" valign="middle">
                                        <a href="{{ config('app.url') }}" style="background-color: #0c4a6e; color: #ffffff; padding: 14px 24px; border-radius: 6px; text-decoration: none; font-size: 13px; font-weight: 700; display: inline-block;">Visit Knowledge Hub</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" style="background-color: #f9fafb; padding: 30px; border-top: 1px solid #f1f5f9;">
                            <p style="color: #94a3b8; font-size: 11px; margin: 0; max-width: 400px; line-height: 1.5;">
                                &copy; {{ date('Y') }} NeuronTalks Scientific Community.<br>Exploring discovery and scientific discourse.
                            </p>
                        </td>
                    </tr>
                </table>
                <!--[if (gte mso 9)|(IE)]>
                </td>
                </tr>
                </table>
                <![endif]-->
            </td>
        </tr>
    </table>
</body>
</html>
