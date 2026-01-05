<?php
session_start();

require_once('phpmailer/class.phpmailer.php');
require_once('phpmailer/class.smtp.php');

$msg_status = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    $nume_vizitator = htmlspecialchars(strip_tags($_POST['nume']));
    $email_vizitator = htmlspecialchars(strip_tags($_POST['email']));
    $subiect = htmlspecialchars(strip_tags($_POST['subiect']));
    $mesaj_text = htmlspecialchars(strip_tags($_POST['mesaj']));

    $mail = new PHPMailer(true); 

    try {
        $mail->IsSMTP();
        $mail->SMTPDebug  = 0;              
        $mail->SMTPAuth   = false;          
        $mail->SMTPSecure = "";             
        $mail->Host       = "localhost";    
        $mail->Port       = 25;             

        $mail->SetFrom('sticadawmail@stica.daw.ssmr.ro', 'Contact Site Muzica');
        
        $mail->AddReplyTo($email_vizitator, $nume_vizitator);

        $mail->AddAddress('sticadawmail@stica.daw.ssmr.ro', 'Admin Site');

        $mail->Subject = "Mesaj Site: " . $subiect;
        
        $body  = "<h3>Ai primit un mesaj nou!</h3>";
        $body .= "<p><strong>Nume:</strong> $nume_vizitator</p>";
        $body .= "<p><strong>Email:</strong> $email_vizitator</p>";
        $body .= "<p><strong>Subiect:</strong> $subiect</p>";
        $body .= "<hr>";
        $body .= "<p><strong>Mesaj:</strong><br>" . nl2br($mesaj_text) . "</p>";

        $mail->MsgHTML($body);

        $mail->Send();
        
        $msg_status = "<div class='alert alert-success'>✅ Mesajul a fost trimis cu succes! Îți vom răspunde în curând.</div>";

    } catch (phpmailerException $e) {
        $msg_status = "<div class='alert alert-danger'>❌ Eroare la trimitere. Te rugăm să încerci mai târziu.</div>";
    } catch (Exception $e) {
        $msg_status = "<div class='alert alert-danger'>❌ Eroare generală.</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="ro">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact - Casa de Producție</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('img/5897b9a30f875b1db5872894c6a23e0b.jpg');
            background-size: cover;
            background-attachment: fixed;
            font-family: 'Segoe UI', Tahoma, sans-serif;
        }
        .contact-card {
            background: white;
            border-radius: 15px;
            padding: 40px;
            margin-top: 50px;
            margin-bottom: 50px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.3);
        }
        .form-label { font-weight: bold; color: #333; }
        .btn-submit {
            background-color: #0d6efd;
            color: white;
            padding: 10px 30px;
            font-size: 1.1rem;
            border-radius: 50px;
            transition: all 0.3s;
        }
        .btn-submit:hover { background-color: #0b5ed7; transform: scale(1.05); }
    </style>
</head>
<body>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            
            <div class="contact-card">
                <div class="text-center mb-4">
                    <h1 style="color: #0d6efd;">📬 Contactează-ne</h1>
                    <p class="text-muted">Ai o întrebare sau o propunere de colaborare? Scrie-ne!</p>
                </div>

                <?php echo $msg_status; ?>

                <form action="contact.php" method="POST">
                    
                    <div class="mb-3">
                        <label for="nume" class="form-label">Numele tău:</label>
                        <input type="text" class="form-control" id="nume" name="nume" placeholder="Ex: Marius Popescu" required>
                    </div>

                    <div class="mb-3">
                        <label for="email" class="form-label">Adresa de Email:</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="nume@exemplu.com" required>
                    </div>

                    <div class="mb-3">
                        <label for="subiect" class="form-label">Subiect:</label>
                        <select class="form-select" id="subiect" name="subiect">
                            <option value="Alege o optiune"> 👀 Alege o optiune</option>
                            <option value="Colaborare">🤝 Colaborare</option>
                            <option value="Inchiriere Studio">🎙️ Închiriere Studio</option>
                            <option value="Intrebare Generala">❓ Întrebare Generală</option>
                            <option value="Feedback">💡 Feedback / Sugestie</option>
                            <option value="Alt raspuns">🙌 Alt raspuns..</option> 
                        </select>
                    </div>

                    <div class="mb-3">
                        <label for="mesaj" class="form-label">Mesajul tău:</label>
                        <textarea class="form-control" id="mesaj" name="mesaj" rows="5" placeholder="Salut, aș dori detalii despre..." required></textarea>
                    </div>

                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-submit">Trimite Mesajul 🚀</button>
                    </div>
                </form>

                <div class="text-center mt-4">
                    <a href="index.html" style="text-decoration: none;">&larr; Înapoi la Pagina Principală</a>
                </div>
            </div>

        </div>
    </div>
</div>

</body>
</html>
