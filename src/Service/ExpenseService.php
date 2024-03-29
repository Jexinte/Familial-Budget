<?php

namespace App\Service;


use App\Entity\Expense;
use App\Entity\SpendingProfile;
use App\Repository\ExpenseRepository;
use App\Repository\SpendingProfileRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;


class ExpenseService extends  AbstractController {
    public function getCleanData(Request $request):array
    {
        $spendingProfileData = ["name" => "","budget" => "","description" => ""];
        $reqArr = $request->toArray();
        foreach($reqArr as $k => $v){
            if($k === count($reqArr) - 1)
            {
                $spendingProfileData["name"] = current($v);
                $spendingProfileData['budget'] = next($v);
                $spendingProfileData['description'] = next($v);
                unset($reqArr[count($reqArr) - 1]);
            }
        }
        return [$spendingProfileData,$reqArr];
    }
    public function saveProfileAndExpenses(Request $request,SpendingProfileRepository $profileRepository,ExpenseRepository $expenseRepository): bool
    {
        $status = false;
        $expensesData = $this->getCleanData($request)[1];
        if(!$this->isSpendingProfileNameAlreadyExist($profileRepository,current($this->getCleanData($request))['name'])){
        $spendingProfile = $this->saveProfile($request,$profileRepository);

            foreach($expensesData as $expenseArr)
            {
                $expense = new Expense();
                $expense->setName($expenseArr['name']);
                $expense->setAmount(floatval($expenseArr['amount']));
                $expense->setCategory($expenseArr['category']);
                $expense->setSpendingProfile($spendingProfile);
                $expense->setPriority($expenseArr['priority']);
                $expenseRepository->getEm()->persist($expense);
            }

            $expenseRepository->getEm()->flush();
            $status = true;
        }
        return $status;

    }

    public function saveProfile(Request $request,SpendingProfileRepository $profileRepository):SpendingProfile
    {
        $spendingProfileData = current($this->getCleanData($request));

        $spending = new SpendingProfile();
        $spending->setName($spendingProfileData['name']);
        $spending->setBudget($spendingProfileData['budget']);
        $spending->setDescription($spendingProfileData['description']);
        $spending->setUser($this->getUser());
        $spending->setSlug($spending->getName());
        $profileRepository->getEm()->persist($spending);
        $profileRepository->getEm()->flush();
        return $spending;
    }

    public function isSpendingProfileNameAlreadyExist(SpendingProfileRepository $profileRepository,string $spendingProfileName):bool
    {
        return is_object($profileRepository->findOneBy(["name" => $spendingProfileName]));
    }

    public function totalAmountExpenses (ExpenseRepository $expenseRepository,SpendingProfile $spendingProfile) : int
    {
        $total = 0;
        $expenses = $expenseRepository->findBy(["spendingProfile" => $spendingProfile]);

        switch (true)
        {
            case count($expenses) > 1:
                foreach ($expenses as $expense){
                    $total+=$expense->getAmount();
                }
            break;

            default:
                $total = current($expenses)->getAmount();
                break;
        }
        return $total;

    }
}

