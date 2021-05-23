<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\LoanApplication;

class LoanOperationsTest extends TestCase
{
    /**
     * A basic feature test example.
     *
     * @return void
     */
    // use RefreshDatabase;

      /** @test */
      public function check_apply_loan_route_access_without_api_token()
      {
        $response=$this->postJson('/api/loan/apply',[]);
        $response
            ->assertStatus(401)
            ->assertJson([
                'message' =>true,
            ]);
      }

       /** @test */
       public function check_change_loan__application_status_access_without_api_token()
       {
         $response=$this->putJson('/api/loan/change-status',[]);
         $response
             ->assertStatus(401)
             ->assertJson([
                'message' =>true,
            ]);
       }

       /** @test */
       public function check_add_loan_payment_access_without_api_token()
       {
         $response=$this->postJson('/api/loan/payment',[]);
         $response
             ->assertStatus(401)
             ->assertJson([
                        'message' =>true,
                    ]);
       }

       /** @test */
        public function check_apply_loan_route_with_api_token_without_credentials()
        {   
            $token=$this->create_user_get_api_token();
            $response=$this->withHeaders([
                'Bearer'=>$token,
            ])->postJson('/api/loan/apply',[]);
            $response
                ->assertStatus(422)
                ->assertJson([
                    'message' =>true,
                    'errors' =>true
                ]);
        }

        /** @test */
        public function check_change_loan_status_with_api_token_without_credentials()
        {   
            $token=$this->create_user_get_api_token();
            $response=$this->withHeaders([
                'Bearer'=>$token,
            ])->putJson('/api/loan/change-status',[]);
            $response
                ->assertStatus(422)
                ->assertJson([
                    'message' =>true,
                    'errors' =>true
                ]);
        }

         /** @test */
        public function check_add_loan_payment_with_api_token_without_credentials()
        {   
            $token=$this->create_user_get_api_token();
            $response=$this->withHeaders([
                'Bearer'=>$token,
            ])->postJson('/api/loan/payment',[]);
            $response
                ->assertStatus(422)
                ->assertJson([
                    'message' =>true,
                    'errors' =>true
                ]);
        }



         /** @test */
         public function check_apply_loan_route_with_api_token_with_missing_credentials()
         {   
             $token=$this->create_user_get_api_token();
             $response=$this->withHeaders([
                 'Bearer'=>$token,
             ])->postJson('/api/loan/apply',[]);
             $response
                 ->assertStatus(422)
                 ->assertJson([
                     'message' =>true,
                     'errors' =>true
                 ]);
         }
 
         /** @test */
         public function check_change_loan_status_with_api_token_with_missing_credentials()
         {   
             $token=$this->create_user_get_api_token();
             $response=$this->withHeaders([
                 'Bearer'=>$token,
             ])->putJson('/api/loan/change-status',[]);
             $response
                 ->assertStatus(422)
                 ->assertJson([
                     'message' =>true,
                     'errors' =>true
                 ]);
         }
         
          /** @test */
         public function check_add_loan_payment_with_api_token_with_missing_credentials()
         {   
             $token=$this->create_user_get_api_token();
             $response=$this->withHeaders([
                 'Bearer'=>$token,
             ])->postJson('/api/loan/payment',[]);
             $response
                 ->assertStatus(422)
                 ->assertJson([
                     'message' =>true,
                     'errors' =>true
                 ]);
         }


          /** @test */
          public function check_apply_loan_route_with_api_token_with_all_credentials_but_incorrect_data_type()
          {   
              $token=$this->create_user_get_api_token();
              $response=$this->withHeaders([
                  'Bearer'=>$token,
              ])->postJson('/api/loan/apply',['amount'=>'abc','loan_term'=>2]);
              $response
                  ->assertStatus(422)
                  ->assertJson([
                      'message' =>true,
                      'errors' =>true
                  ]);
          }

         
          /** @test */
         public function check_add_loan_payment_with_api_token_with_all_credentials_but_incorrect_data_type()
         {   
             $token=$this->create_user_get_api_token();
             $response=$this->withHeaders([
                 'Bearer'=>$token,
             ])->postJson('/api/loan/payment',['loan_application_id'=>'123444444','amount'=>'aa']);
             $response
                 ->assertStatus(422)
                 ->assertJson([
                     'message' =>true,
                     'errors' =>true
                 ]);
         }

         ///No Need in Change Status
        
          /** @test */
          public function check_apply_loan_route_with_api_token_with_all_correct_credentials()
          {   
              $token=$this->create_user_get_api_token();
              $response=$this->withHeaders([
                  'Bearer'=>$token,
              ])->postJson('/api/loan/apply',['amount'=>1000,'loan_term'=>2]);
              $response
                  ->assertStatus(200)
                  ->assertJson([
                      'message' =>true,
                      'reference_id' =>true
                  ]);
          }

             /** @test */
         public function check_change_loan_status_with_api_token_with_incorrect_credentials_permutations()
         {   
             $token=$this->create_user_get_api_token();
             $applicationId=$this->apply_loan_get_application_id($token);
             //wrong application
             $response=$this->withHeaders([
                 'Bearer'=>$token,
             ])->putJson('/api/loan/change-status',['status'=>'completed','loan_application_id'=>'1234']);
             $response
                 ->assertStatus(422)
                 ->assertJson([
                     'message' =>true,
                 ]);


            //right application wrong status
            $response=$this->withHeaders([
            'Bearer'=>$token,
                ])->putJson('/api/loan/change-status',['status'=>'completed','loan_application_id'=>$applicationId]);
                $response
                    ->assertStatus(422)
                    ->assertJson([
                        'message' =>true,
                    ]);
         }

           /** @test */
         public function check_change_loan_status_with_api_token_with_correct_credentials()
         {   
             $token=$this->create_user_get_api_token();
             $applicationId=$this->apply_loan_get_application_id($token);
             $response=$this->withHeaders([
                 'Bearer'=>$token,
             ])->putJson('/api/loan/change-status',['status'=>'approved','loan_application_id'=>$applicationId]);
             $response
                 ->assertStatus(200)
                 ->assertJson([
                     'message' =>true,
                 ]);
         }

         /** @test */
        public function check_add_loan_payment_with_api_token_with_correct_credentials_but_token_for_other_user_application_status_not_approved()
        {   
            $token=$this->create_user_get_api_token();
            $token2=$this->create_user_get_api_token();
            $applicationId=$this->apply_loan_get_application_id($token);
            
            $response=$this->withHeaders([
                'Bearer'=>$token2,
            ])->postJson('/api/loan/payment',['loan_application_id'=>$applicationId,'amount'=>500]);
            $response
                ->assertStatus(422)
                ->assertJson([
                    'message' =>true,
                ]);
        }

        //   /** @test */
          public function check_add_loan_payment_with_api_token_with_correct_credentials_but_token_for_other_user_application_status_approved()
          {   
              $token=$this->create_user_get_api_token();
              $token2=$this->create_user_get_api_token();
              $applicationId=$this->apply_loan_get_application_id($token);
              $application=LoanApplication::where('reference_id',$applicationId)->first();
              $application->status='approved';
              $application->save();

              
              $response=$this->withHeaders([
                  'Bearer'=>$token2,
              ])->postJson('/api/loan/payment',['loan_application_id'=>$applicationId,'amount'=>500]);
              $response
                  ->assertStatus(401)
                  ->assertJson([
                      'message' =>true,
                  ]);
          }

        /** @test */
        public function check_add_loan_payment_with_api_token_with_correct_credentials_application_status_not_approved()
        {   
            $token=$this->create_user_get_api_token();
            $applicationId=$this->apply_loan_get_application_id($token);
            $response=$this->withHeaders([
                'Bearer'=>$token,
            ])->postJson('/api/loan/payment',['loan_application_id'=>$applicationId,'amount'=>500]);
            $response
                ->assertStatus(422)
                ->assertJson([
                    'message' =>true,
                ]);
        }

           /** @test */
           public function check_add_loan_payment_with_api_token_with_correct_credentials_application_status_approved()
           {   
               $token=$this->create_user_get_api_token();
               $applicationId=$this->apply_loan_get_application_id($token);
               $application=LoanApplication::where('reference_id',$applicationId)->first();
               $application->status='approved';
               $application->save();
               $response=$this->withHeaders([
                   'Bearer'=>$token,
               ])->postJson('/api/loan/payment',['loan_application_id'=>$applicationId,'amount'=>500]);
               $response
                   ->assertStatus(200)
                   ->assertJson([
                       'message' =>true,
                   ]);
           }

        ///////////////////////////////////////
        private function create_user_get_api_token()
        {
            $user=User::factory()->create(['password'=>bcrypt('12345678')]);
            $response=$this->postJson('/api/login',['email'=>$user->email,'password'=>'12345678']);
            $access_token= $response->decodeResponseJson()['access_token'];
            
            return $access_token;
        }
       
        private function apply_loan_get_application_id($token)
        {
            $response=$this->withHeaders([
                'Bearer'=>$token,
            ])->postJson('/api/loan/apply',['amount'=>1000,'loan_term'=>2]);
                
            $reference_id= $response->decodeResponseJson()['reference_id'];

            return $reference_id;                
        }
}
