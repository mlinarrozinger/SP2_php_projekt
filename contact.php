<?php include 'header.php'; ?>

<main class="container mt-5">

    <h2>Kontakt</h2>
    <p class="text-muted">Imate vprašanje? Pišite nam.</p>

    <div class="row mt-4">
        <div class="col-md-6">

            <form method="POST" action="#">

                <div class="mb-3">
                    <label class="form-label">Ime</label>
                    <input type="text" name="name" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">E-pošta</label>
                    <input type="email" name="email" class="form-control" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Sporočilo</label>
                    <textarea name="message" class="form-control" rows="5" required></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Pošlji</button>

            </form>

        </div>

        <div class="col-md-6">
            <h5>Kontaktni podatki</h5>
            <p>Email: info@knjigarna.si</p>
            <p>Telefon: 040 123 456</p>
            <p>Naslov: Ljubljana, Slovenija</p>
        </div>
    </div>

</main>

<?php include 'footer.php'; ?>
