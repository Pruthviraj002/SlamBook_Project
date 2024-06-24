<?php
if (isset($_SESSION["uloggedin"]) && $_SESSION["uloggedin"] == true) {
    $uloggedin = true;
} else {
    $uloggedin = false;
}
?>

<div class="bg-sky-950 flex h-10 justify-items-center">
    <img src="img/DS.png" class="h-auto justify-center align-middle">
    <div class="text-white flex-row p-2 border border-white rounded-md m-0 font-bold shadow-white shadow-inner hover:text-sky-300 active:font-semisbold select-none"
        id="home" onclick="menu()">Menu</div>
    <div id="options" class="hidden">
        <?php
        if (!$uloggedin) {
            echo '
                <div class="text-white flex-row m-2 font-bold hover:text-sky-300 active:font-semisbold select-none"><a href="usignup.php" onclick="playClickSound()">Signup</a></div>
                <div class="text-white flex-row m-2 font-bold hover:text-sky-300 active:font-semisbold select-none"><a href="ulogin.php" onclick="playClickSound()">Login</a></div>';
        }

        if ($uloggedin) {

            $hashedAdminId = hash('sha256', $_SESSION['admin_id']);

            echo '
                <div class="text-white flex-row m-2 font-bold hover:text-sky-300 active:font-semisbold select-none"><a href="logout.php" onclick="playClickSound()" title="">Logout</a></div>';
        }
        ?>
    </div>
</div>

<audio id="clickSound" src="clickSound.mp3"></audio>

<script>
    const home = document.getElementById("home");
    const options = document.getElementById("options");
    const clickSound = document.getElementById("clickSound");
    let isOpen = false;

    function menu() {
        if (isOpen) {
            options.style.display = "none";

        } else {
            options.style.display = "flex";
            // Play the click sound
            clickSound.play();
        }
        isOpen = !isOpen;
    }

    function playClickSound() {
        // Play the click sound
        clickSound.play();
    }
</script>