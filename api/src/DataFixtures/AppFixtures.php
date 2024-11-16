<?php

namespace App\DataFixtures;

use App\Entity\Car;
use App\Entity\Company;
use App\Entity\Customer;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $this->loadFakeCompanies($manager);
        $this->loadFakeUsers($manager);
        $this->loadFakeCustomers($manager);
        $this->loadFakeCars($manager);
    }

    public function loadFakeCompanies(ObjectManager $manager): void
    {
        $company = new Company();
        $company->setName('S.C. GARAGE SRL');

        $companyRepository = $manager->getRepository(Company::class);
        $companyRepository->save($company);
    }

    public function loadFakeUsers(ObjectManager $manager): void
    {
        $userRepository = $manager->getRepository(User::class);
        $companyRepository = $manager->getRepository(Company::class);
        $company = $companyRepository->findByName('S.C. GARAGE SRL');
        $usernames = ['alex', 'dragos', 'dodo', 'mitzi'];
        $password = $_ENV['FAKE_USERS_PASSWORD'];

        foreach ($usernames as $username) {
            $user = new User();
            $user->setUsername($username);
            $user->setPassword($password);
            $user->setFirstName($username);
            $user->setLastName($username);
            $user->setCompany($company);

            $userRepository->save($user);
        }
    }

    public function loadFakeCustomers(ObjectManager $manager): void
    {
        $customerRepository = $manager->getRepository(Customer::class);
        $companyRepository = $manager->getRepository(Company::class);

        $company = $companyRepository->findByName('S.C. GARAGE SRL');
        $customerNames = ['alex', 'dragos', 'dodo', 'mitzi'];
        $phoneNumbers = ['0000 000 000', '1111 111 111 111', '2222 222 222', '3333 333 333'];

        foreach ($customerNames as $index => $customerName) {
            $customer = new Customer();
            $customer->setFirstName($customerName);
            $customer->setLastName($customerName);
            $customer->setCompany($company);
            $customer->setPhoneNumber($phoneNumbers[$index]);

            $customerRepository->save($customer);
        }
    }

    public function loadFakeCars(ObjectManager $manager): void
    {
        $carRepository = $manager->getRepository(Car::class);
        $customerRepository = $manager->getRepository(Customer::class);

        $cars = ['Audi', 'BMW', 'Audi', 'VW'];
        $plates = ['DJ-11-AAA', 'DJ-22-BBB', 'DJ-33-CCC', 'DJ-44-DDD'];
        $customers = ['alex', 'dragos', 'dodo', 'mitzi'];

        foreach ($cars as $index => $car) {
            $customer = $customerRepository->findByName($customers[$index]);
            $car = new Car();
            $car->setCarType(1);
            $car->setLicensePlate($plates[$index]);
            $car->setCustomer($customer);

            $carRepository->save($car);
        }
    }
}
