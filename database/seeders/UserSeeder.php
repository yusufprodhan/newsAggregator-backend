<?php
namespace Database\Seeders;
use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeders.
     */
    public function run()
    {

        // Default password
        $defaultPassword = app()->environment('production') ? Str::random() : '123456';

        // Create demo user
        $user = new User();
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        $user->truncate();

        $data = $user->create([
            'name' => 'Test user',
            'email' => 'testmail@mail.com',
            'password' => bcrypt($defaultPassword),
            'gender' => 'male',
            'about_me' => 'Here is about me.',
            'occupation' => 'Senior Software engineer',
            'is_admin' => false,
            'city' => 'Munich',
            'state' => 'Bavaria',
            'status' => true,
            'locale' => app()->getLocale(),
            'timezone' => config('app.timezone'),
            'email_verified_at' => now(),
        ]);
        $data->save();
    }
}
