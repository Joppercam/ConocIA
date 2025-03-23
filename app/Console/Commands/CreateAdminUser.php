<?php
namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'admin:create 
        {email? : The email of the admin user} 
        {--p|password= : The password for the admin user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Solicitar email si no se proporcionó
        $email = $this->argument('email') ?? $this->ask('Enter admin email');

        // Validar email
        $validator = Validator::make(['email' => $email], [
            'email' => 'required|email|unique:users,email'
        ]);

        if ($validator->fails()) {
            $this->error('Invalid email or email already exists.');
            return 1;
        }

        // Solicitar nombre
        $name = $this->ask('Enter admin name');

        // Solicitar contraseña si no se proporcionó
        $password = $this->option('password') ?? $this->secret('Enter admin password');

        // Validar contraseña
        $passwordValidator = Validator::make(['password' => $password], [
            'password' => 'required|min:8|regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/'
        ]);

        if ($passwordValidator->fails()) {
            $this->error('Password must be at least 8 characters long and include uppercase, lowercase, number, and special character.');
            return 1;
        }

        // Confirmar contraseña
        $confirmPassword = $this->secret('Confirm admin password');

        if ($password !== $confirmPassword) {
            $this->error('Passwords do not match.');
            return 1;
        }

        // Crear usuario administrador
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'role' => 'admin',
            'status' => 'active'
        ]);

        $this->info("Admin user created successfully!");
        $this->info("Email: {$email}");

        return 0;
    }
}