<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Change Password</title>
    <link href="https://cdn.tailwindcss.com" rel="stylesheet" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap"
        rel="stylesheet"
    />
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="flex font-poppins items-center justify-center">
<div class="h-screen w-screen flex justify-center items-center dark:bg-gray-900">
    <div class="grid gap-8">
        <div
            id="back-div"
            class="bg-gradient-to-r from-blue-500 to-purple-500 rounded-[26px] m-4"
        >
            <div
                class="border-[20px] border-transparent rounded-[20px] dark:bg-gray-900 bg-white shadow-lg xl:p-10 2xl:p-10 lg:p-10 md:p-10 sm:p-2 m-2"
            >
                <h1 class="pt-8 pb-6 font-bold dark:text-gray-400 text-5xl text-center cursor-default">
                    Change Password
                </h1>
                <?php if (!empty($error)) : ?>
                    <p class="text-red-500 text-center font-medium"><?= $error; ?></p>
                <?php endif; ?>
                <form action="change-password-handler.php" method="post" class="space-y-4">
                    <div>
                        <label for="current-password" class="mb-2 dark:text-gray-400 text-lg">Sent Code</label>
                        <input
                            id="sent-code"
                            class="border p-3 shadow-md dark:bg-indigo-700 dark:text-gray-300 dark:border-gray-700 placeholder:text-base focus:scale-105 ease-in-out duration-300 border-gray-300 rounded-lg w-full"
                            type="text"
                            placeholder="Enter the sent code"
                            name="sent_code"
                            required
                        />
                    </div>
                    <div>
                        <label for="new-password" class="mb-2 dark:text-gray-400 text-lg">New Password</label>
                        <input
                            id="new-password"
                            class="border p-3 shadow-md dark:bg-indigo-700 dark:text-gray-300 dark:border-gray-700 placeholder:text-base focus:scale-105 ease-in-out duration-300 border-gray-300 rounded-lg w-full"
                            type="password"
                            placeholder="Enter your new password"
                            name="new_password"
                            required
                        />
                    </div>
                    <div>
                        <label for="confirm-password" class="mb-2 dark:text-gray-400 text-lg">Confirm New Password</label>
                        <input
                            id="confirm-password"
                            class="border p-3 shadow-md dark:bg-indigo-700 dark:text-gray-300 dark:border-gray-700 placeholder:text-base focus:scale-105 ease-in-out duration-300 border-gray-300 rounded-lg w-full"
                            type="password"
                            placeholder="Confirm your new password"
                            name="confirm_password"
                            required
                        />
                    </div>
                    <button
                        class="bg-gradient-to-r dark:text-gray-300 from-blue-500 to-purple-500 shadow-lg mt-6 p-2 text-white rounded-lg w-full hover:scale-105 hover:from-purple-500 hover:to-blue-500 transition duration-300 ease-in-out"
                        type="submit"
                    >
                        CHANGE PASSWORD
                    </button>
                </form>
                <div class="flex flex-col mt-4 items-center justify-center text-sm">
                    <a
                        class="group text-blue-400 transition-all duration-100 ease-in-out"
                        href="profile.php"
                    >
                        <span>Back to Profile</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
