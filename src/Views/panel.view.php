<?php

$output = '
<header class="flex-shrink-0">
    <nav class="navbar">
        <a class="navbar-brand mr-auto" href="' . $host . '">Управление задачами</a>
        <div class="ml-auto d-flex">
        <a class="nav-link ml-auto" href="' . $loginAddress . '">Вход</a>
';

if ($showRegisterAddress) {
    $output .= '
        <a class="nav-link" href="' . $registerAddress . '">Регистрация</a>
    ';
}

$output .= '
        </div>
    </nav>
</header>
';

return $output;
