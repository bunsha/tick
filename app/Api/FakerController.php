<?php

namespace App\Api;

use App\Account;
use App\Action;
use App\Activity;
use App\Address;
use App\Contact;
use App\Entity;
use App\EntityData;
use App\Lead;
use App\Phone;
use App\Tag;
use App\User;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;

class FakerController extends Controller
{
    public $faker;

    public function __construct(){
        require base_path('vendor/autoload.php');
        require_once base_path('vendor/fzaninotto/faker/src/autoload.php');
        $this->faker = $faker = \Faker\Factory::create();
    }

    public function clear(){
//        DB::delete('DELETE FROM tags WHERE  id > 0');
//        DB::delete('DELETE FROM lead_user WHERE  user_id > 0;');
//        DB::delete('DELETE FROM activities WHERE  id > 0;');
//        DB::delete('DELETE FROM entity_data WHERE  id > 0;');
//        DB::delete('DELETE FROM phones WHERE  id > 0;');
//        DB::delete('DELETE FROM addresses WHERE  id > 0;');
//        DB::delete('DELETE FROM contacts WHERE  id > 0;');
//        DB::delete('DELETE FROM entities WHERE  id > 0;');
//        DB::delete('DELETE FROM leads WHERE  id > 0;');
//        DB::delete('DELETE FROM users WHERE  id > 0;');
//        DB::delete('DELETE FROM accounts WHERE  id > 0;');
    }


    public function start(){
        $this->entities(1000);
    }



    public function leads($num, $userId, $entity){
        for ($i=0; $i < $num; $i++){
            $item = new Lead();
            $item->progress = $this->faker->numberBetween(5, 90);
            $item->active = $this->faker->numberBetween(0, 1);
            $item->level = $this->faker->numberBetween(0, 7);
            $item->activity_setting = null;
            $item->next_action = $this->faker->dateTimeBetween($startDate = 'now', $endDate = '+1 week');
            $item->save();
            $user = User::find($userId);
            $user->leads()->attach($item->id);
            $item->entity_id = $entity;
            $item->save();
            $account = Account::find($user->account_id);
            $account->entities()->attach($entity);
        }
    }


    public function entities($num = 1000){
        for ($i=0; $i < $num; $i++) {
            $item = new Entity();
            $item->type = $this->faker->randomElement($array = array ('company', 'person'));
            if($item->type == 'company')
                $item->name = $this->faker->company;
            else
                $item->name = $this->faker->name;
            $item->account_id = 1;
            $item->save();
            $this->addresses(1, $item->id);
            $this->entity_data(8, $item->id);
            $this->contacts($this->faker->numberBetween(1, 3), $item->id);
            $this->leads($this->faker->numberBetween(1, 10), 1, $item->id);
        }
    }

    public function addresses($num, $entity_id){
        for ($i=0; $i < $num; $i++) {
            $item = new Address();
            $item->city = $this->faker->city;
            $item->state = $this->faker->state;
            $item->zip = $this->faker->postcode;
            $item->street = $this->faker->streetName;
            $item->building = $this->faker->buildingNumber;
            $item->apartment = $this->faker->numberBetween(0, 200);
            $item->full_string = $this->faker->address;
            $item->lat = $this->faker->latitude;
            $item->lng = $this->faker->longitude;
            $item->country = $this->faker->country;
            $item->entity_id = $entity_id;
            $item->timezone = $this->faker->timezone;
            $item->save();
        }
    }

    public function entity_data($num, $entity_id){
        for ($i=0; $i < $num; $i++) {
            $item = new EntityData();
            $item->type = $this->faker->randomElement($array = array ('input', 'select', 'text'));
            if($item->type == 'input'){
                $item->name = $this->faker->randomElement($array = array ('Sales Volume', 'License', 'Group', 'Product Type', 'Credit Rating', 'Service'));
                $item->key = strtolower($item->name);
                $item->value = $this->faker->word();
            }elseif($item->type == 'select'){
                $item->name = $this->faker->randomElement($array = array ('Sales Volume', 'License', 'Group', 'Product Type', 'Credit Rating', 'Service'));
                $item->key = strtolower($item->name);
                $item->value = $this->faker->word();
            }else{
                $item->name = 'about';
                $item->key = strtolower($item->name);
                $item->value = $this->faker->text(30);
            }
            $item->entity_id = $entity_id;
            $item->save();
        }
    }

    public function contacts($num, $entity_id){
        for ($i=0; $i < $num; $i++) {
            $item = new Contact();
            $item->first_name = $this->faker->firstName;
            $item->last_name = $this->faker->lastName;
            $item->type = $this->faker->randomElement($array = array ('office', 'employee'));
            $item->entity_id = $entity_id;
            if($item->type != 'office')
            $item->title = $this->faker->title;
            $item->email = $this->faker->email;
            $item->save();
            $this->phones($this->faker->numberBetween(1, 3), $item->id);
        }
    }

    public function phones($num, $contact_id = null){
        for ($i=0; $i < $num; $i++) {
            $item = new Phone();
            $item->number = $this->faker->phoneNumber;
            $item->code = $this->faker->numberBetween(100, 999);;
            $item->type = $this->faker->randomElement($array = array ('mobile', 'home', 'office'));
            $item->available_from = $this->faker->time($format = 'H:i', $max = 'now');
            $item->available_to = $this->faker->time($format = 'H:i', $max = 'now');
            $item->contact_id = $contact_id;
            $item->save();
        }
    }
}
