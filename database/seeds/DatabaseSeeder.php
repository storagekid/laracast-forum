<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $this->call(ChannelSeeder::class);
        $this->call(UsersSeeder::class);
        $this->call(RealUsersSeeder::class);
        $this->call(ThreadsSeeder::class);
        $this->call(RepliesSeeder::class);
    }
}
