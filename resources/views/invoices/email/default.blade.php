<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Your Email Subject</title>
</head>

<div style="margin-top:0; margin-right:0; margin-bottom:0; margin-left:0; padding:0; background-color:#f4f4f4;">

    <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="border-collapse:collapse; background-color:#f4f4f4;">
        <tr>
            <td align="center" style="padding:20px 10px 20px 10px;">

                <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:600px; border-collapse:collapse; background-color:#ffffff;">

                    <!-- Header -->
                    <tr>
                        <td align="center" style="padding:20px 30px 10px 30px; border-bottom:1px solid #e5e5e5;">
                            <a href="https://example.com" target="_blank" style="text-decoration:none;">
                                <img
                                        src="%%INVOICE_LOGO_SRC%%"
                                        alt="Company Logo"
                                        border="0"
                                        style="display:block; width:160px; max-width:100%; height:auto;"
                                        width="160"
                                />
                            </a>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td align="left" style="padding:25px 30px 20px 30px; font-family:Arial, Helvetica, sans-serif; color:#555555;">
                            <div style="font-size:14px; line-height:20px;">
                                {!! $messageHtml !!}
                            </div>
                        </td>
                    </tr>

                    <!-- Divider -->
                    <tr>
                        <td style="padding:0 30px;">
                            <hr style="border:none; border-top:1px solid #e5e5e5; margin-top:0; margin-right:0; margin-bottom:0; margin-left:0;" />
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td align="center" style="padding:15px 30px 25px 30px; font-family:Arial, Helvetica, sans-serif; color:#999999; font-size:11px; line-height:16px;">

                            <p style="margin-top:0; margin-right:0; margin-bottom:4px; margin-left:0;">
                                You are receiving this email because you opted in on our website.
                            </p>

                            <p style="margin-top:0; margin-right:0; margin-bottom:4px; margin-left:0;">
                                <a href="https://example.com/preferences" target="_blank" style="color:#999999; text-decoration:underline;">Manage preferences</a>
                                &nbsp;|&nbsp;
                                <a href="https://example.com/unsubscribe" target="_blank" style="color:#999999; text-decoration:underline;">Unsubscribe</a>
                            </p>

                            <p style="margin-top:0; margin-right:0; margin-bottom:0; margin-left:0;">
                                {{app(\App\Settings\CompanySettings::class)->name}},
                                {{app(\App\Settings\CompanySettings::class)->address_line_1}},
                                {{app(\App\Settings\CompanySettings::class)->city}},
                                {{app(\App\Settings\CompanySettings::class)->state}},
                                {{app(\App\Settings\CompanySettings::class)->country}}
                            </p>

                        </td>
                    </tr>

                </table>

            </td>
        </tr>
    </table>

</div>
</html>