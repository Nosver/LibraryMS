<?php
session_start(); 
require("connect.php");
require("header.php");
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];

    $qry = "SELECT * FROM users WHERE username='" . htmlspecialchars($username) . "'";
    $result = myQuery($qry);

    if ($result && $result->num_rows > 0) {
        $user = $result->fetch_assoc(); 
        $hashedPassword = $user['password'];

        if (password_verify(htmlspecialchars($password), $hashedPassword)) {
            $_SESSION['user'] = $user; 

            if ($user['role'] == "CUSTOMER") {
                header("Location: library/bookCatalogue.php"); //denemek için değiştirdim
            } elseif ($user['role'] == "STAFF") {
                header("Location: library/staff.php");
            }
            exit; 
        } else {
            $error= "Invalid username or password.";
        }
    } else {
        $error=  "Invalid username or password.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link href="./output.css" rel="stylesheet" />
    <title>Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet"
    />
     <script src="https://cdn.tailwindcss.com"></script>

</head>

<body class="flex font-poppins items-center justify-center">
<div class="h-screen w-screen flex justify-center items-center ">
    <div class="grid gap-8">
        <div
            id="back-div"
            class="bg-gradient-to-r from-blue-500 to-purple-500 rounded-[26px] m-4"
        >
            <div
                class="border-[20px] border-transparent rounded-[20px] bg-white shadow-lg xl:p-10 2xl:p-10 lg:p-10 md:p-10 sm:p-2 m-2"
            >
                <h1 class="pt-8 pb-6 font-bold  text-5xl text-center cursor-default">
                    Log in
                </h1>
                 <?php if (!empty($error)) : ?>
                    <p class="text-red-500 text-center font-medium"><?= $error; ?></p>
                <?php endif; ?>
                <form action="#" method="post" class="space-y-4">
                    <div>
                        <label for="username" class="mb-2  text-lg">Username</label>
                        <input
                            id="username"
                            class="border p-3  shadow-md placeholder:text-base focus:scale-105 ease-in-out duration-300 border-gray-300 rounded-lg w-full"
                            type="username"
                            placeholder="Username"
                            name="username"
                            required
                        />
                    </div>
                    <div>
                        <label for="password" class="mb-2  text-lg">Password</label>
                        <input
                            id="password"
                            class="border p-3 shadow-md     placeholder:text-base focus:scale-105 ease-in-out duration-300 border-gray-300 rounded-lg w-full"
                            type="password"
                            placeholder="Password"
                            name="password"
                            required
                        />
                    </div>
                    <a
                        class="group text-blue-400 transition-all duration-100 ease-in-out"
                        href="#"
                    >
              <span
                  class=""
              >
                Forget your password?
              </span>
                    </a>
                    <button
                        class="bg-gradient-to-r  from-blue-500 to-purple-500 shadow-lg mt-6 p-2 text-white rounded-lg w-full hover:scale-105 hover:from-purple-500 hover:to-blue-500 transition duration-300 ease-in-out"
                        type="submit"
                    >
                        LOG IN
                    </button>
                </form>
                <div class="flex flex-col mt-4 items-center justify-center text-sm">
                    <h3 class="">
                        Don't have an account?
                        <a
                            class="group text-blue-400 transition-all duration-100 ease-in-out"
                            href="register.php"
                        >
                <span
                    class=""
                >
                  Create one
                </span>
                        </a>
                    </h3>
                </div>

                <div
                    class="text-gray-500 flex text-center flex-col mt-4 items-center text-sm"
                >

                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
