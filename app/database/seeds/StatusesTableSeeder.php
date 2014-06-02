<?php

class StatusesTableSeeder extends Seeder {

    public function run() {
        DB::table('statuses')->delete();
        \Status::create(array('name' => \Status::EN_COURS));
        \Status::create(array('name' => \Status::TERMINE));
        \Status::create(array('name' => \Status::TERMINE_VO));
        \Status::create(array('name' => \Status::STOPPE));
    }
}