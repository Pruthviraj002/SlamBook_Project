<?php
if (isset($_SESSION["loggedin"]) && $_SESSION["loggedin"] == true) {
    $loggedin = true;
} else {
    $loggedin = false;
}
?>

<div class="bg-sky-950 flex h-10 justify-items-center">
    <img src="img/DS.png" class="h-auto justify-center align-middle">
    <div class="text-white flex-row p-2 border border-white rounded-md m-0 font-bold shadow-white shadow-inner hover:text-sky-300 active:font-semisbold select-none"
        id="home" onclick="menu()">Menu</div>
    <div id="options" class="hidden">
        <?php
        echo '<div class="text-white flex-row m-2 font-bold hover:text-sky-300 active:font-semisbold select-none"
onclick="playClickSound()"><a href="about.php" >About</a></div>';
        if (!$loggedin) {
            echo '
                <div class="text-white flex-row m-2 font-bold hover:text-sky-300 active:font-semisbold select-none"><a href="signup.php" >Signup</a></div>
                <div class="text-white flex-row m-2 font-bold hover:text-sky-300 active:font-semisbold select-none"><a href="login.php" >Login</a></div>';
        }

        if ($loggedin) {

            $hashedAdminId = hash('sha256', $_SESSION['admin_id']);

            echo '
                <div class="text-white flex-row m-2 font-bold hover:text-sky-300 active:font-semisbold select-none"><a href="home.php" title="Share Link">Link</a></div>
                <div class="text-white flex-row m-2 font-bold hover:text-sky-300 active:font-semisbold select-none"><a href="myslambook.php" title="My Slambook">Slambook</a></div>
                <div class="text-white flex-row m-2 font-bold hover:text-sky-300 active:font-semisbold select-none"><a href="badge.php?admin_id=' . $hashedAdminId . '" title="Badges Achieved">Badges</a></div>
                <div class="text-white flex-row m-2 font-bold hover:text-sky-300 active:font-semisbold select-none"><a href="logout.php" title="">Logout</a></div>';
        }
        ?>
    </div>
</div>

<audio id="clickSound" src="a4.mp3"></audio>

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