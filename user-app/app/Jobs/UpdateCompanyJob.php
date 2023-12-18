<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Company;

class UpdateCompanyJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    protected $objectId;
    protected $propertyName;
    protected $newPropertyValue;

    /**
     * Create a new job instance.
     */
    public function __construct($objectId, $propertyName, $newPropertyValue)
    {
        $this->objectId = $objectId;
        $this->propertyName = $propertyName;
        $this->newPropertyValue = $newPropertyValue;
    }

    /**
     * Execute the job.
     */
    public function handle()
    {
        $objectId = $this->objectId;
        $propertyName = $this->propertyName;
        $newPropertyValue = $this->newPropertyValue;

        $company = Company::where('company_id', $objectId)->first();
        if ($company) {
            $company->{$this->propertyName} = $this->newPropertyValue;
            $company->save();
        }
    }
}