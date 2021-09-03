<?php

namespace App\DataFixtures;

use Faker\Factory;
use App\Entity\Invoice;
use App\Entity\Customer;
use App\Entity\User;
use Doctrine\Persistence\ObjectManager;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    /**
     * Encoder le password du mon User
     *
     * @var UserPasswordEncoderInterface
     */
    private $encoder;
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }
    public function load(ObjectManager $manager)
    {
       $faker   = Factory::create('fr_FR');
       
       for($u = 1; $u < 10; $u++){
           $user   = new User();
           $chrono  = 1;
           $hash    = $this->encoder->encodePassword($user,"pass");
           $user->setFirstName($faker->firstName())
                ->setLastName($faker->lastName)
                ->setEmail($faker->email)
                ->setPassword($hash);
            $manager->persist($user);
       

        for ($i=0;$i<mt_rand(5,10);$i++){
            $customer = new Customer();
            $customer->setFirstName($faker->firstName())
                        ->setLastName($faker->lastName)
                        ->setCompany($faker->company)
                        ->setEmail($faker->email);
                $manager->persist($customer);
                for($j = 0; $j<mt_rand(3,10);$j++){
                    $invoice = new Invoice();
                    $invoice->setAmount($faker->randomFloat(2,250,5000))
                            ->setSentAt($faker->dateTimeBetween('-6months'))
                            ->setStatus($faker->randomElement(['SENT','PAID','CANCELED']))
                            ->setCustomer($customer)
                            ->setChrono($chrono);
                        $chrono++;
                    $manager->persist($invoice);
                    
                }
            }
        }   
         $manager->flush();
    }
}
