<?php

namespace KovacsZoltan65\LaravelEnumsToJson\Commands;

use BackedEnum;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Spatie\StructureDiscoverer\Discover;
use KovacsZoltan65\LaravelEnumsToJson\Attributes\EnumToJson;
use KovacsZoltan65\LaravelEnumsToJson\Exceptions\LaravelEnumToJsonException;

class LaravelEnumsToJsonCommand extends Command
{
    public $signature = 'enums-to-json:generate';

    public $description = 'My command';

    public function handle(): int
    {
        $output = [];

        $this->getQualifiedEnums()
            ->each(function(BackedEnum|string $enum) use(&$output) {
                $fileName = str($enum)->snake()->append('.json')->toString();
                if($output[$fileName] ?? false){
                    throw LaravelEnumToJsonException::nameCollision($fileName);
                }
                $output[$fileName] = $this->prepareEnumData($enum);
            });

        foreach($output as $fileName => $contents) {
            Storage::disk(config('enums-to-json.disk'))
                ->put(
                    str(config('enums-to-json.path') . '/' . $fileName)
                        ->replace('//', '')
                        ->toString(),
                    json_encode($contents)
                );
        }

        $this->comment('Generated ' . count($output) . ' files');

        return self::SUCCESS;
    }

    protected function getQualifiedEnums(): Collection
    {
        $enums = Discover::in(...config('enums-to-json.enum_location'))
            ->enums()
            ->withAttribute(EnumToJson::class)
            ->get();

        return collect($enums);
    }

    protected function prepareEnumData(BackedEnum|string $enum)
    {
        return collect($enum::cases())
                ->map(function($el) {
                    return [
                        'label' => $el->label,
                        'value' => $el->value,
                    ];
                });
    }

    protected function generateFileName(BackedEnum|string $enum)
    {
        if(method_exists($enum, 'jsonFileName')) {
            return str($enum::jsonFileName())->finish('.json')->toString();
        }

        return str($enum)->classBasename()->snake()->append('.json')->toString();
    }
}
