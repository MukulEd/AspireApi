<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class AuthenticationTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */

     use RefreshDatabase;

    /** @test */
    public function login_validation_errors_without_data()
        {
            $response=$this->postJson('/api/login',[]);
            $response
                ->assertStatus(422)
                ->assertJson([
                    'errors' => true,
                    'message' =>true,
                ]);
        }

        /** @test */
        public function login_validation_errors_with_wrong_data()
        {
            $response=$this->postJson('/api/login',['email'=>'mukul@gmail.com','password'=>'12345678']);
            $response
                ->assertStatus(422)
                ->assertJson([
                    'message' =>'Invalid Credentials',
                ]);
        }

        /** @test */
        public function login_validation_errors_with_wrong_data_type()
        {
            $response=$this->postJson('/api/login',['email'=>'mukul.com','password'=>'12345678']);
            $response
                ->assertStatus(422)
                ->assertJson([
                    'message' =>true,
                    'errors' =>['email'=>true],
                ]);
        }
        /** @test */
        public function login_validation_with_inactive_user(){
            
            $user=User::factory()->state(['status'=>0])->make();
            $response=$this->postJson('/api/login',['email'=>$user->email,'password'=>$user->password]);
            $response
                ->assertStatus(422)
                ->assertJson([
                    'message' =>'Invalid Credentials',
                ]);
        }

            /** @test */
        public function login_validation_with_active_user(){
            
            $user=User::factory()->create(['password'=>bcrypt('12345678')]);
            $response=$this->postJson('/api/login',['email'=>$user->email,'password'=>'12345678']);
            $response
                ->assertStatus(200)
                ->assertJson([
                    'user' => true,
                    'access_token' =>true,
                    'token_type'=>'Bearer'
                ]);
                
        }


        /** @test */
        public function logout_without_api_token(){

            $response=$this->postJson('/api/logout',[]);
            $response
                ->assertStatus(401)
                ->assertJson([
                    'message' => true,
                ]);
                
        }

            /** @test */
        public function logout_with_api_token(){

            $user=User::factory()->create(['password'=>bcrypt('12345678')]);
            $response=$this->postJson('/api/login',['email'=>$user->email,'password'=>'12345678']);
            $access_token= $response->decodeResponseJson()['access_token'];
        
            $response=$this->withHeaders([
                'Bearer'=>$access_token,
            ])->postJson('/api/logout',[]);
            $response
                ->assertStatus(200)
                ->assertJson([
                    'message' => true,
                ]);
                
        }
}
