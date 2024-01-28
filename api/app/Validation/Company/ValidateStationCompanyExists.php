<?php

declare(strict_types=1);

namespace App\Validation\Company;

use App\Models\Company;
use Illuminate\Validation\Validator;

class ValidateStationCompanyExists
{
    public function __invoke(Validator $validator)
    {
        $companyId = $validator->getValue('companyId');

        if (! $companyId) {
            // if we are here then we are in a PATCH operation
            // in the POST operation we have the "required" validation rule
            return;
        }

        $validator->stopOnFirstFailure();

        // @phpstan-ignore-next-line
        $companyExists = ! (\DB::table((new Company())->getTable())
                               ->select('id')
                               ->where('id', '=', $companyId)
                               ->get())->isEmpty();

        if (! $companyExists) {
            $validator->errors()
                      ->add('company_id', 'Company with given ID does not exist');
        }
    }
}
