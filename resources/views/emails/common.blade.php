
<!DOCTYPE html>
<html>
<head>

</head>
<body marginheight="0">
<link href="https://fonts.googleapis.com/css?family=Montserrat:300,300i,400,400i,500,500i,600,600i,700,700i&display=swap" rel="stylesheet">

<meta charset="utf-8">
<style type="text/css">
    body {font-family: "Montserrat", sans-serif;}
</style>
<div style="background: #fff;font-family: arial">
    <header style="border-top:1px solid #8048144f;border-bottom:1px solid #8048144f; text-align: center ; padding: 0px 25px ; padding-top: 1% ; padding-bottom: 1%;
    margin: 20px 0px; display: flex;justify-content: center;background-color:#4D4D4D">
     <ul style="display:flex;flex-wrap: wrap;justify-content: center;list-style: none;padding-left: 0px;text-align: center; margin:auto;">
        <li style="padding: 0px 10px;">
             <a href="<?php echo url('/') ?>"  target="_blank">
        <img src="{{asset('admin/images/dash-log.png')}}" alt="site-logo">
        </a>
        </li>
    </ul>

    </header>
    <table style="width:100%; border-collapse:collapse; border-spacing:0; border-collapse:collapse; display:table; text-align:left;">
        <tbody>
            <tr>
                <td class="message_box" style="padding: 15px; font-size: 14px ; color: #1b1b1b;">
                <?php   echo $content; ?>

                  <footer style="background-color:#4D4D4D;border-top:1px solid #8048144f;border-bottom:1px solid #8048144f; text-align: center ; padding: 0px 25px ; padding-top: 1% ; padding-bottom: 1%;
                      margin: 20px 0px; display: flex;justify-content: center;">

                         <ul style="display:flex;flex-wrap: wrap;justify-content: center;list-style: none;padding-left: 0px;text-align: center; margin:auto;">
                            <li style="padding: 0px 10px;border-right: 2px solid #e8e8e8;">
                                <a href="<?php echo config("Settings.website"); ?>" style="color: #FDBF5A!important;text-decoration: none;font-size: 12px;text-transform: uppercase;text-align: center;border-radius: 3px;color: #000;display: block;margin: 5px;color: #FDBF5A;font-weight: 600;">
                                    Website
                                </a>
                            </li>
                            <li style="padding: 0px 10px;border-right: 2px solid #e8e8e8;">
                                <a href="<?php echo url('/contact-us') ?>" style="color: #FDBF5A!important;text-decoration: none;font-size: 12px;text-transform: uppercase;text-align: center;border-radius: 3px;color: #000;display: block;margin: 5px;color: #FDBF5A;font-weight: 600;">
                                  Contact us
                                </a>
                            </li>
                            <li style="padding: 0px 10px;border-right: 2px solid #e8e8e8;">
                                <a href="<?php echo url('/privacy-policy') ?>" style="color: #FDBF5A!important;text-decoration: none;font-size: 12px;text-transform: uppercase;text-align: center;border-radius: 3px;color: #000;display: block;margin: 5px;color: #FDBF5A;font-weight: 600;">
                                  Privacy
                                </a>
                            </li>
                            <li style="padding: 0px 10px;">
                                <a href="<?php echo url('/term-&-condition') ?>" style="color: #FDBF5A!important;text-decoration: none;font-size: 12px;text-transform: uppercase;text-align: center;border-radius: 3px;color: #000;display: block;margin: 5px;color: #FDBF5A;font-weight: 600;">
                                 Terms
                                </a>
                            </li>

                        </ul>
                    </footer>

                </td>
            </tr>
        </tbody>
    </table>
</div>
</body>
</html>
