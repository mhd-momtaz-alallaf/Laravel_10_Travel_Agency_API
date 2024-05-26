<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Console\View\Components\Choice;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

class CreateUserCommand extends Command // this artisan command is for create a new user into database (create the first admin by example).
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Creates a New User';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user['name'] = $this->ask('Enter the name of the new user:');
        $user['email'] = $this->ask('Enter the email of the new user:');
        $user['password'] = $this->secret('Enter the password of the new user:'); // secret will hide the password in the consol.

        $roleName = $this->choice('The Role of the new user',['admin','editor'], /*default*/1); // default 1 => editor.

        $role = Role::where('name', $roleName)->first();
        if( ! $role )
        {
            $this->error('Role not found!');

            return -1; // error code.
        }

        $validator = Validator::make($user, [ // in the artisan commands we cant use request()->validate helper for the validation, we will use Validator Facades instead.
            'name' => ['required', 'string', 'max:255'],
            'email'=> ['required', 'string', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', Password::default()], 
        ]);

        if( $validator->fails() )
        {
            foreach( $validator->errors()->all() as $error )
                $this->error($error);

            return -1;
        }

        DB::transaction(function() use ($user, $role) // to perform the whole operations or it all fails.
        {
            $user['password'] = Hash::make( $user['password'] ); //  we should make the hash after the validation or we can do it in an observer, Hash::make will hash the password.
            $newUser = User::create($user);
            $newUser->roles()->attach($role->id);
        });
        
        $this->info('User '.$user['email'].' Created Successfully!');

        return 0; // success code.
    }
}
