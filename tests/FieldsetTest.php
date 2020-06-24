<?php

namespace Mediconesystems\LivewireDatatables\Tests;

use Mediconesystems\LivewireDatatables\Field;
use Mediconesystems\LivewireDatatables\Fieldset;
use Mediconesystems\LivewireDatatables\Tests\TestCase;
use Mediconesystems\LivewireDatatables\Tests\Models\DummyModel;

class FieldsetTest extends TestCase
{
    /** @test */
    public function it_can_generate_an_array_of_fields_from_a_model()
    {
        factory(DummyModel::class)->create();

        $subject = Fieldset::fromModel(DummyModel::class);

        $this->assertCount(8, $subject->fields());
        $this->assertEquals('dummy_models', $subject->table);

        $subject->fields()->each(function ($field) {
            $this->assertIsObject($field, Field::class);
        });
    }

    /**
     * @test
     * @dataProvider fieldDataProvider
     */
    public function it_can_correctly_populate_the_fields_from_the_model($name, $index, $column)
    {
        factory(DummyModel::class)->create();

        $subject = Fieldset::fromModel(DummyModel::class)->fields();

        $this->assertEquals($name, $subject[$index]->name);
        $this->assertEquals($column, $subject[$index]->column);
        $this->assertNull($subject[$index]->callback);
        $this->assertNull($subject[$index]->selectFilter);
        $this->assertNull($subject[$index]->booleanFilter);
        $this->assertNull($subject[$index]->textFilter);
        $this->assertNull($subject[$index]->dateFilter);
        $this->assertNull($subject[$index]->timeFilter);
        $this->assertNull($subject[$index]->hidden);
    }

    public function fieldDataProvider()
    {
        return [
            ['Id', 0, 'dummy_models.id'],
            ['Relation_id', 1, 'dummy_models.relation_id'],
            ['Subject', 2, 'dummy_models.subject'],
            ['Body', 3, 'dummy_models.body'],
            ['Flag', 4, 'dummy_models.flag'],
            ['Expires_at', 5, 'dummy_models.expires_at'],
            ['Created_at', 6, 'dummy_models.created_at'],
            ['Updated_at', 7, 'dummy_models.updated_at'],
        ];
    }

    /** @test */
    public function it_can_exclude_fields()
    {
        factory(DummyModel::class)->create();

        $subject = Fieldset::fromModel(DummyModel::class)
            ->except(['dummy_models.id', 'dummy_models.body'])
            ->fields();

        $this->assertCount(6, $subject);

        $this->assertArrayNotHasKey(0, $subject);
        $this->assertArrayNotHasKey(3, $subject);
    }

    /** @test */
    public function it_can_rename_fields_from_the_model()
    {
        factory(DummyModel::class)->create();

        $subject = Fieldset::fromModel(DummyModel::class)
            ->rename(['dummy_models.id' => 'ID'])
            ->fields();

        $this->assertEquals('ID', $subject[0]->name);
        $this->assertEquals('dummy_models.id', $subject[0]->column);
    }
}
