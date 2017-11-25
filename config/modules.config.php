<?php
return [
    // Внешние модули
    'DoctrineModule',
    'DoctrineORMModule',
    'AssetManager',
    'TwbBundle',
    // Мои иодули
    'MCms',             // Свои хелперы и пр.

    'Files',            // Модуль должен быть в начале списка (иначе может возникнуть конфликт роутов)

    // Модули с моделями базы данных
    'Settings',
    'Fields',
    'Menu',
    'Pages',
    'News',
    'Emails',
    'Sensors',
    'Users',

    'Navigator',        // Отвечает за навигацию
    'AuthDoctrine',     // Отвечает за авторизацию и ACL (распределение прав доступа)

    'Application',
    'Admin',
];
