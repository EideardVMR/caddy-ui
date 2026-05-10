<?php

class UserDB
{

    static private $users = null;

    static public $current_user = null;
    public static array $errors = [];

    static function getUserList(): array
    {

        if (self::$users !== null) {
            return self::$users;
        }

        $users_raw = file_get_contents(FILE_USERS);
        $users_json = json_decode($users_raw);

        self::$users = [];
        foreach ($users_json as $p) {
            $user = new User();
            $user->MapData($p);
            self::$users[$p->username] = $user;
        }

        return self::$users;
    }

    static function addUser(string $username, string $name, string $pw, bool $active): int|false
    {
        $user = new User();
        $user->username = $username;
        $user->name = $name;
        $user->active = $active;
        
        $user->SetPassword($pw);
        if (self::$users === null) {
            self::getUserList();
        }

        if($user->validate()) {
            self::$users[$username] = $user;
            return self::saveUserList();
        } else {
            return false;
        }
    }

    static function editUser(User $user): int|false
    {
        self::$users[$user->username] = $user;
        return self::saveUserList();
    }

    static function saveUserList(): int|false
    {
        $user_raw = json_encode(self::$users);
        return file_put_contents(FILE_USERS, $user_raw);
    }

    static function getUserByName(string $username): User | false
    {
        if (self::$users === null) {
            self::getUserList();
        }
        if (!isset(self::$users[$username])) {
            return false;
        }
        return self::$users[$username];
    }

    static function login(string $username, string $password): bool
    {

        $user = self::getUserByName($username);
        if ($user === false) {
            return false;
        }
        if ($user->active === false) {
            return false;
        }
        if ($user->CheckPassword($password) === false) {
            return false;
        }

        $user->session_token = strrand(512);

        setcookie("auth", base64_encode($username . "[;]" . $user->session_token), time() + (86400 * 30), "/");

        self::saveUserList();

        return true;
    }

    static function verifyAuth(): bool
    {

        if (!isset($_COOKIE['auth'])) {
            return false;
        }

        $tmp = base64_decode($_COOKIE['auth']);
        $tmp = explode('[;]', $tmp);
        if (count($tmp) != 2) {
            return false;
        }

        $user = self::getUserByName($tmp[0]);
        if ($user === false) {
            return false;
        }
        if ($user->active === false) {
            return false;
        }

        if ($user->session_token != $tmp[1]) {
            return false;
        }

        self::$current_user = $user;

        return true;
    }
}

class User
{

    public $name = "";
    public $username = "";
    public $password = "";
    public $password_salt = "";
    public $session_token = "";
    public bool $active = false;

    public array $errors = [];

    public function MapData(StdClass $data)
    {
        $this->name = $data->name ?? '';
        $this->username = $data->username ?? '';
        $this->password = $data->password ?? '';
        $this->password_salt = $data->password_salt ?? '';
        $this->session_token = $data->session_token ?? '';
        $this->active = $data->active ?? false;
    }

    public function CheckPassword(string $pw): bool
    {

        return password_verify($pw . $this->password_salt, $this->password);
    }

    public function validate(): bool
    {
        //$this->errors = [];
        if (empty($this->name)) {
            $this->errors[] = "Name darf nicht leer sein";
        }

        if (empty($this->username)) {
            $this->errors[] = "Benutzername darf nicht leer sein";
        }

        if (!preg_match('/^[a-zA-Z0-9]+$/', $this->username)) {
            $this->errors[] = "Benutzername darf nur Buchstaben und Zahlen enthalten";
        }

        if (empty($this->password) || empty($this->password_salt)) {
            $this->errors[] = "Password falsch gefüllt";
        }

        return count($this->errors) == 0;
    }

    public function SetPassword(string $pw)
    {
        $this->password_salt = strrand(25);
        $this->password = password_hash($pw . $this->password_salt, PASSWORD_DEFAULT);
    }
}
