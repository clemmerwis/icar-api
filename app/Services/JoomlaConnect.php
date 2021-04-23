<?php

namespace App\Services;

use Http;

use App\Models\VehicleMake;
use App\Models\VehicleModel;
use App\Models\VehicleYear;
use App\Models\VehicleSubmodel;
use App\Models\YearMakeModelConfig;
use App\Models\Article;
use App\Models\Vehicle;
use App\Models\ArticleCategory;

class JoomlaConnect
{
    public $categories = [3,4,78,80];

    public function getYearsMakesModels()
    {
        $req = Http::get('https://icar.webcitzdevelopment.com/webcitz-search.php?datatype=ymm');
        return $req->json();
    }

    public function getArticles($catid)
    {
        $req = Http::get('https://icar.webcitzdevelopment.com/webcitz-search.php?datatype=articles&catid=' . $catid);
        return $req->json();
    }

    public function getCategories()
    {
        $req = Http::get('https://icar.webcitzdevelopment.com/webcitz-search.php?datatype=categories');
        return $req->json();
    }

    public function syncArticles($categories = [])
    {
        // $this->syncCategories();
        $categories = count($categories) ? $categories : $this->categories;
        foreach($categories as $cat) {
            $data = $this->getArticles($cat);
            $articleCategory = ArticleCategory::where('joomla_id', $cat);
            $yearId = YearMakeModelConfig::yearId();
            $modelId = YearMakeModelConfig::modelId();
            $makeId = YearMakeModelConfig::makeId();
            $submodelId = YearMakeModelConfig::submodelId();

            
            foreach($data as $d) {
              $xfields = collect($d['x_fields']);
              $vehicle = [
                  'year' => null,
                  'make' => null,
                  'model' => null,
                  'submodel' => null
              ];

            foreach($xfields as $field) {
                $id = (int) $field['id'];
                $vsearch = is_array($field['value']) ? (int) $field['value'][0] : (int) $field['value'];
                if($id == $yearId) {
                $vyear = VehicleYear::find($vsearch);
                if($vyear) {
                    $vehicle['year'] = $vyear->name;
                }
                }
                if($id == $modelId) {
                $vmodel = VehicleModel::find($vsearch);
                if($vmodel) {
                    $vehicle['model'] = $vmodel->name;
                }
                }
                if($id == $makeId) {
                $vmake = VehicleMake::find($vsearch);
                if($vmake) {
                    $vehicle['make'] = $vmake->name;
                }
                }
                if($id == $submodelId) {
                $vsubmodel = VehicleSubmodel::find($vsearch);
                if($vsubmodel) {
                    $vehicle['submodel'] = $vsubmodel->name;
                }
                }
            }

            $this->syncVehicle($vehicle);

            $url = $d['alias'];

            if($articleCategory->exists()) {
              $url = '/' . $articleCategory->first()->joomla_url . '/' . $d['alias'];
            }

            $article = Article::updateOrCreate(
                ['joomla_id' => (int) $d['id']],
                [
                    'alias' => $d['alias'],
                    'joomla_url' => $url,
                    'title' => $d['title'],
                    'year' => $vehicle['year'],
                    'make' => $vehicle['make'],
                    'model' => $vehicle['model'],
                    'submodel' => $vehicle['submodel'],
                    'joomla_data' => $d['x_fields'],
                    'category' => (int) $cat
                ]
            );
            }
        }
    }

    public function syncVehicle($data)
    {
      if($data['make'] !== null && $data['model'] !== null) {
        $v = Vehicle::firstOrCreate(
          ['make' => $data['make'], 'model' => $data['model'], 'year' => $data['year']]
        );
      }
      
    }

    public function syncYmm($type = 'all')
    {
      $data = $this->getYearsMakesModels();

      $this->updateConfig($data);

      $ref = [
        'years' => 'syncYears',
        'models' => 'syncModels',
        'makes' => 'syncMakes',
        'submodels' => 'syncSubmodels'
      ];

      if($type == 'all') {
        $this->syncYears($data['years']['values']);
        $this->syncMakes($data['makes']['values']);
        $this->syncModels($data['models']['values']);
        $this->syncSubmodels($data['submodels']['values']);
      } else {
        $method = $ref[$type];
        $params = $data[$type]['values'];
        $this->$method($params);
      }
      
    }

    private function syncYears(array $years)
    {
      foreach($years as $year) {
        VehicleYear::updateOrCreate(
            ['name' => $year['name']],
            ['joomla_id' => $year['value']]
        );
      }
    }

    private function syncMakes(array $makes)
    {
      foreach($makes as $make) {
        VehicleMake::updateOrCreate(
            ['name' => $make['name']],
            ['joomla_id' => $make['value']]
        );
      }
    }

    private function syncModels(array $models)
    {
      foreach($models as $model) {
        VehicleModel::updateOrCreate(
            ['joomla_id' => $model['value']],
            ['name' => $model['name']]
        );
      }
    }

    private function syncSubmodels(array $submodels)
    {
      foreach($submodels as $model) {
        VehicleSubmodel::updateOrCreate(
            ['joomla_id' => $model['value']],
            ['name' => $model['name']]
        );
      }
    }

    public function syncCategories()
    {
        $data = $this->getCategories();
        foreach($data as $cat) {
            $id = (int) $cat['id'];
            ArticleCategory::updateOrCreate(
                ['joomla_id' => $id],
                ['name' => $cat['name'], 'joomla_url' => $cat['alias']]
            );
        }
    }

    public function updateConfig($data)
    {
      $year = $data['years']['ref_id'];
      $make = $data['makes']['ref_id'];
      $model = $data['models']['ref_id'];
      $submodel = $data['submodels']['ref_id'];
      YearMakeModelConfig::updateOrCreate(
            ['name' => 'year'],
            ['joomla_id' => (int) $year]
      );
      YearMakeModelConfig::updateOrCreate(
            ['name' => 'make'],
            ['joomla_id' => (int) $make]
      );
      YearMakeModelConfig::updateOrCreate(
            ['name' => 'model'],
            ['joomla_id' => (int) $model]
      );
      YearMakeModelConfig::updateOrCreate(
            ['name' => 'submodel'],
            ['joomla_id' => (int) $submodel]
      );
    }
}