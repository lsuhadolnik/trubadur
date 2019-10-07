<?php

$filename = "users.csv";
$outFilename = "usersFilledData.csv";

function generatePass() {
    $pass = "";
    for($i = 0; $i < 4; $i++){
        $pass .= chr(rand(65, 90));
    }

    for($i = 0; $i < 4; $i++){
        $pass .= rand(0, 9);
    }

    return $pass;
}

$csvFile = file($filename);
$users = ['id', 'name', 'email', 'grade', 'password'];
foreach ($csvFile as $line) {

    $data = str_getcsv($line);
    
    $name = $data[1];
    $email = $data[2];
    $pass = generatePass();
    $grade = $data[3];

    $u = User::create([
        'name' => $name,
        'email' => $email,
        'password' => bcrypt($pass),
        'verified' => 1,
        'school_id' => 2,
        'grade_id' => $grade
    ]);

    // 0 : id
    // 1 : name
    // 2 : email
    // 3 : grade
    // 4 : pass

    $users[] = [$u->id, $name, $email, $grade];
}

fputcsv(fopen($outFilename, 'w'), $users);