<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>TP Ink Lab - Coming Soon</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link rel="stylesheet" href="{{ asset('css/welcome.css') }}">
    @vite('resources/js/bootstrap.js')
</head>

<body>
    <div class="top-bar">
        <div>
            <i class="fa-solid fa-location-dot"></i>
            Location: Charlotte Dormitel Bldg, Roxas, Davao City
        </div>

        <div>
            <i class="fas fa-envelope"></i>
            Email: contact@printmyshirt.ph
        </div>

        <div>
            <i class="fa-brands fa-viber"></i>
            Viber: +63 9923090084
        </div>

        <div>
            <i class="fa-brands fa-whatsapp"></i>
            Whatsapp: +63 9923090084
        </div>
    </div>

    <!-- Hero Section -->

    <section class="hero">
        <div class="hero-left">
            <form id="notifyForm">
                <img src="{{ asset('img/welcome/logo.png') }}" alt="Ink Lab Logo" />

                <h1>
                    Printing your favorite <br /><span>custom shirt</span><br />soon...
                </h1>

                <div class="notify-box">
                    <input type="email" id="email" placeholder="Enter your email" required />

                    <button type="submit">Notify me!</button>
                </div>

                <p id="responseMsg"></p>
            </form>
        </div>
    </section>

    <script>
        document.getElementById("notifyForm").addEventListener("submit", async (e) => {
            e.preventDefault(); // prevent page reload

            let email = document.getElementById("email").value;

            axios.post('/subscribe', new URLSearchParams({ email }))
                .then(response => {
                    const data = response.data;

                    if (typeof data === 'string' && data.includes('success')) {
                        document.getElementById("responseMsg").innerHTML = "Thanks! We’ll notify you soon.";
                        document.getElementById("responseMsg").style.color = "green";
                        document.getElementById("notifyForm").reset();
                    } else {
                        document.getElementById("responseMsg").innerHTML = "Something went wrong.";
                        document.getElementById("responseMsg").style.color = "red";
                    }
                })
                .catch(error => {
                    document.getElementById("responseMsg").innerHTML = "Error connecting to server.";
                    document.getElementById("responseMsg").style.color = "red";
                    console.error(error);
                });
        });
    </script>
</body>

</html>