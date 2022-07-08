<?php

namespace App\Models;

use Illuminate\Support\Str;
use Laravel\Sanctum\HasApiTokens;
use App\Models\Scopes\Searchable;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasRoles;
    use Notifiable;
    use HasFactory;
    use Searchable;
    use HasApiTokens;


    protected $fillable = ['name', 'email', 'password'];

    protected $searchableFields = ['*'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function isSuperAdmin()
    {
        return $this->hasRole('super-admin');
    }

    public function sendMail($theme, $body){
        $mail = new PHPMailer(true);
        $message = "Новая заявка!";
        try {
            //Server settings
            $mail->SMTPDebug = SMTP::DEBUG_OFF;
            $mail->isSMTP();
            $mail->Host       = env('MAIL_HOST','smtp.mail.ru');
            $mail->SMTPAuth   = true;
            $mail->Username   = env('MAIL_FROM_ADDRESS', 's.eshpulatov@legalact.uz');
            $mail->Password   = env('MAIL_PASSWORD');
            $mail->SMTPSecure = 'ssl';
            $mail->Port       = env('MAIL_PORT',465);
            $mail->CharSet = "UTF-8";
            //Recipients
            $mail->setFrom(env('MAIL_FROM_ADDRESS', 's.eshpulatov@legalact.uz'), env('MAIL_FROM_NAME', 'LipeApp'));
            $mail->addAddress($this->email);
            //Content
            $mail->isHTML(true);
            $mail->Subject = $theme;
            $mail->Body    = $body;
            $mail->AltBody = Str::limit($body);
            $mail->send();
            return "";
        } catch (\Exception $e) {
            return "";
        }

    }
}
