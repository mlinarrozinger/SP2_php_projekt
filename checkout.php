<?php
session_start();
include 'db.php';
include 'header.php';

require_once __DIR__ . '/mail_config.php';
require_once __DIR__ . '/vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$successMessage = '';
$errorMessage = '';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = array();
}

$cart = $_SESSION['cart'];
$cartItems = array();
$totalItems = 0;

/*
    Preberi knjige iz baze glede na košarico
*/
if (!empty($cart)) {
    foreach ($cart as $bookID => $qty) {
        $bookID = (int)$bookID;
        $qty = (int)$qty;

        $sql = "SELECT BookID, Name, Author FROM Book WHERE BookID = $bookID LIMIT 1";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0) {
            $book = $result->fetch_assoc();
            $book['qty'] = $qty;
            $cartItems[] = $book;
            $totalItems += $qty;
        }
    }
}

/*
    Oddaja naročila
*/
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $customerName = isset($_POST['customer_name']) ? trim($_POST['customer_name']) : '';
    $customerEmail = isset($_POST['customer_email']) ? trim($_POST['customer_email']) : '';

    if (empty($cartItems)) {
        $errorMessage = 'Košarica je prazna.';
    } elseif ($customerName === '' || $customerEmail === '') {
        $errorMessage = 'Izpolni vsa obvezna polja.';
    } elseif (!filter_var($customerEmail, FILTER_VALIDATE_EMAIL)) {
        $errorMessage = 'E-poštni naslov ni veljaven.';
    } else {
        $subject = 'Potrdilo naročila - spletna knjigarna';

        $body = "Pozdravljeni " . $customerName . ",\n\n";
        $body .= "Vaše naročilo je sprejeto. Hvala za naročilo.\n\n";
        $body .= "Povzetek naročila:\n";

        foreach ($cartItems as $item) {
            $body .= "- " . $item['Name'] . " (" . $item['Author'] . "), količina: " . $item['qty'] . "\n";
        }

        $body .= "\nSkupaj knjig: " . $totalItems . "\n";
        $body .= "\nLep pozdrav,\nSpletna knjigarna";

        $mail = new PHPMailer(true);

        try {
            $mail->CharSet = 'UTF-8';
            $mail->isSMTP();
            $mail->Host = SMTP_HOST;
            $mail->SMTPAuth = true;
            $mail->Username = SMTP_USER;
            $mail->Password = SMTP_PASS;

            if ((int)SMTP_PORT === 465) {
                $mail->SMTPSecure = 'ssl';
            } else {
                $mail->SMTPSecure = 'tls';
            }

            $mail->Port = SMTP_PORT;

            $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
            $mail->addAddress($customerEmail, $customerName);
            $mail->addReplyTo(SMTP_FROM, SMTP_FROM_NAME);

            $mail->Subject = $subject;
            $mail->Body = $body;
            $mail->isHTML(false);

            $mail->send();

            $_SESSION['cart'] = array();
            $cartItems = array();

            $successMessage = 'Naročilo je bilo uspešno oddano. Potrdilo je bilo poslano na e-mail.';
        } catch (Exception $e) {
            $errorMessage = 'Pošiljanje e-pošte ni uspelo: ' . $mail->ErrorInfo;
        }
    }
}
?>

    <main class="container mt-4">
        <h2>Zaključek nakupa</h2>
        <hr>

        <?php if ($successMessage !== ''): ?>
            <div class="alert alert-success">
                <?php echo htmlspecialchars($successMessage); ?>
            </div>

            <a href="index.php" class="btn btn-primary">Nazaj na začetno stran</a>

        <?php else: ?>

            <?php if ($errorMessage !== ''): ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($errorMessage); ?>
                </div>
            <?php endif; ?>

            <?php if (empty($cartItems)): ?>
                <p>Košarica je prazna.</p>
                <a href="books.php" class="btn btn-secondary">Nazaj na knjige</a>
            <?php else: ?>

                <div class="card mb-4">
                    <div class="card-header">Povzetek naročila</div>
                    <div class="card-body">
                        <table class="table table-bordered">
                            <thead>
                            <tr>
                                <th>Naslov</th>
                                <th>Avtor</th>
                                <th>Količina</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php foreach ($cartItems as $item): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($item['Name']); ?></td>
                                    <td><?php echo htmlspecialchars($item['Author']); ?></td>
                                    <td><?php echo (int)$item['qty']; ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>

                        <h5>Skupaj knjig: <?php echo $totalItems; ?></h5>
                    </div>
                </div>

                <form method="POST" action="checkout.php">
                    <div class="mb-3">
                        <label for="customer_name" class="form-label">Ime in priimek</label>
                        <input type="text" name="customer_name" id="customer_name" class="form-control" required>
                    </div>

                    <div class="mb-3">
                        <label for="customer_email" class="form-label">E-poštni naslov</label>
                        <input type="email" name="customer_email" id="customer_email" class="form-control" required>
                    </div>

                    <button type="submit" class="btn btn-success">Potrdi naročilo</button>
                    <a href="cart.php" class="btn btn-secondary">Nazaj v košarico</a>
                </form>

            <?php endif; ?>

        <?php endif; ?>
    </main>

<?php include 'footer.php'; ?>