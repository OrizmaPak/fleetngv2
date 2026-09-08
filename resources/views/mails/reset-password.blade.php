
<!DOCTYPE html>
<html lang="en" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">

  <head>
    <meta charset="utf-8">
    <meta name="x-apple-disable-message-reformatting">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="format-detection" content="telephone=no, date=no, address=no, email=no">
    <title>Set your Password</title>
    <link href="https://fonts.googleapis.com/css?family=Montserrat:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,500;1,600;1,700" rel="stylesheet" media="screen">
    <style>      
      .hover-underline:hover {
        text-decoration: underline !important;
      }

      @keyframes spin {
        to {
          transform: rotate(360deg);
        }
      }

      @keyframes ping {

        75%,
        100% {
          transform: scale(2);
          opacity: 0;
        }
      }

      @keyframes pulse {
        50% {
          opacity: .5;
        }
      }

      @keyframes bounce {

        0%,
        100% {
          transform: translateY(-25%);
          animation-timing-function: cubic-bezier(0.8, 0, 1, 1);
        }

        50% {
          transform: none;
          animation-timing-function: cubic-bezier(0, 0, 0.2, 1);
        }
      }

      @media (max-width: 600px) {
        .sm-px-24 {
          padding-left: 24px !important;
          padding-right: 24px !important;
        }

        .sm-py-32 {
          padding-top: 32px !important;
          padding-bottom: 32px !important;
        }

        .sm-w-full {
          width: 100% !important;
        }
      }
    </style>
  </head>

  <body style="margin: 0; padding: 0; width: 100%; word-break: break-word; -webkit-font-smoothing: antialiased; opacity: 1; background-color: #eceff1; background-color: rgba(236, 239, 241, 1);">
    <div style="display: none;">A request to reset password was received from your Imperial Advance Account</div>
    <div role="article" aria-roledescription="email" aria-label="Reset your Password" lang="en">
      <table style="font-family: Montserrat, -apple-system, 'Segoe UI', sans-serif; width: 100%;" width="100%" cellpadding="0" cellspacing="0" role="presentation">
        <tr>
          <td align="center" style="opacity: 1; background-color: #eceff1; background-color: rgba(236, 239, 241, 1); font-family: Montserrat, -apple-system, 'Segoe UI', sans-serif;" bgcolor="rgba(236, 239, 241, 1)">
            <table class="sm-w-full" style="font-family: 'Montserrat',Arial,sans-serif; width: 600px;" width="600" cellpadding="0" cellspacing="0" role="presentation">
              <tr>
                <td class="sm-py-32 sm-px-24" style="font-family: Montserrat, -apple-system, 'Segoe UI', sans-serif; padding: 48px; text-align: center;" align="center">
                  <a href="javascript:void(0);">
                    <img src="{{asset('images/logo/fleetng-logo.svg')}}" width="100" alt="Logo" style="border: 0; max-width: 100%; line-height: 100%; vertical-align: middle;">
                  </a>
                </td>
              </tr>
              <tr>
                <td align="center" class="sm-px-24" style="font-family: 'Montserrat',Arial,sans-serif;">
                  <table style="font-family: 'Montserrat',Arial,sans-serif; width: 100%;" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                    <tr>
                      <td class="sm-px-24" style="opacity: 1; background-color: #ffffff; background-color: rgba(255, 255, 255, 1); border-radius: 4px; font-family: Montserrat, -apple-system, 'Segoe UI', sans-serif; font-size: 14px; line-height: 24px; padding: 48px; text-align: left; opacity: 1; color: #626262; color: rgba(98, 98, 98, 1);" bgcolor="rgba(255, 255, 255, 1)" align="left">
                        <p style="font-weight: 600; font-size: 18px; margin-bottom: 0;">Hello, {{$name}}</p>
                        <p style="font-weight: 700; font-size: 20px; margin-top: 1; opacity: 1;">Congratulations!</p>
                        <p style="margin: 0 0 24px;">
                          Your {{isset($is_payment_user) && $is_payment_user == 1 ? 'payment' : ''}} user account has been created successfully. Please login from following link after setting up your password. 
                          <span style="font-weight: 600;">fleetNG</span> Account -
                          <a href="{{$email}}" class="hover-underline" style="opacity: 1; color: #7367f0; color: rgba(115, 103, 240, 1); text-decoration: none;">{{$email}}</a>
                          
                        </p>
                        {{-- <p style="margin: 0 0 24px;">Use this link to reset your password and login.</p> --}}
                        <a href="{{$url}}" style="display: block; font-size: 14px; line-height: 18px; margin-bottom: 24px; opacity: 1; color: #7367f0; color: rgba(115, 103, 240, 1); text-decoration: none;">{{$url}}</a>
                        <table style="font-family: 'Montserrat',Arial,sans-serif;" cellpadding="0" cellspacing="0" role="presentation">
                          <tr>
                            <td style="mso-padding-alt: 16px 24px; opacity: 1; background-color: #7367f0; background-color: rgba(115, 103, 240, 1); border-radius: 4px; font-family: Montserrat, -apple-system, 'Segoe UI', sans-serif;" bgcolor="rgba(115, 103, 240, 1)">
                              <a href="{{$url}}" style="display: block; font-weight: 600; font-size: 14px; line-height: 100%; padding: 16px 24px; opacity: 1; color: #ffffff; color: rgba(255, 255, 255, 1); text-decoration: none;">Set Password &rarr;</a>
                            </td>
                          </tr>
                        </table>
                        <p style="margin: 24px 0;">
                          <span style="font-weight: 600;">Note:</span> Link is valid for one hour only.
                        </p>
                      <table style="font-family: 'Montserrat',Arial,sans-serif; width: 100%;" width="100%" cellpadding="0" cellspacing="0" role="presentation">
                          <tr>
                            <td style="font-family: 'Montserrat',Arial,sans-serif; padding-top: 32px; padding-bottom: 32px;">
                              <div style="opacity: 1; background-color: #eceff1; background-color: rgba(236, 239, 241, 1); height: 1px; line-height: 1px;">&zwnj;</div>
                            </td>
                          </tr>
                        </table>
                        <p style="margin: 0 0 16px;">
                          Not sure why you received this email? Please
                          <a href="mailto:support@example.com" class="hover-underline" style="opacity: 1; color: #7367f0; color: rgba(115, 103, 240, 1); text-decoration: none;">let us know</a>.
                        </p>
                        <p style="margin: 0 0 16px;">Thanks, <br>fleetNG Team</p>
                      </td>
                    </tr>
                    <tr>
                      <td style="font-family: 'Montserrat',Arial,sans-serif; height: 20px;" height="20"></td>
                    </tr>
                    <tr>
                      <td style="font-family: Montserrat, -apple-system, 'Segoe UI', sans-serif; font-size: 12px; padding-left: 48px; padding-right: 48px; opacity: 1; color: #eceff1; color: rgba(236, 239, 241, 1);">
                        <p align="center" style="cursor: default; margin-bottom: 16px;">
                          <a href="https://www.facebook.com" style="opacity: 1; color: #263238; color: rgba(38, 50, 56, 1); text-decoration: none;"><img src="images/facebook.png" width="17" alt="Facebook" style="border: 0; max-width: 100%; line-height: 100%; vertical-align: middle; margin-right: 12px;"></a>
                          &bull;
                          <a href="https://twitter.com" style="opacity: 1; color: #263238; color: rgba(38, 50, 56, 1); text-decoration: none;"><img src="images/twitter.png" width="17" alt="Twitter" style="border: 0; max-width: 100%; line-height: 100%; vertical-align: middle; margin-right: 12px;"></a>
                          &bull;
                          <a href="https://www.instagram.com" style="opacity: 1; color: #263238; color: rgba(38, 50, 56, 1); text-decoration: none;"><img src="images/instagram.png" width="17" alt="Instagram" style="border: 0; max-width: 100%; line-height: 100%; vertical-align: middle; margin-right: 12px;"></a>
                        </p>
                        <p style="opacity: 1; color: #263238; color: rgba(38, 50, 56, 1);">
                          Use of our service and website is subject to our
                          <a href="javascript:void(0);" class="hover-underline" style="opacity: 1; color: #7367f0; color: rgba(115, 103, 240, 1); text-decoration: none;">Terms of Use</a> and
                          <a href="javascript:void(0);" class="hover-underline" style="opacity: 1; color: #7367f0; color: rgba(115, 103, 240, 1); text-decoration: none;">Privacy Policy</a>.
                        </p>
                      </td>
                    </tr>
                    <tr>
                      <td style="font-family: 'Montserrat',Arial,sans-serif; height: 16px;" height="16"></td>
                    </tr>
                  </table>
                </td>
              </tr>
            </table>
          </td>
        </tr>
      </table>
    </div>
  </body>
</html>