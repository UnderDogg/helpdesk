<?php

namespace App\Tests\Resource;

use App\Models\Guide;
use App\Models\GuideStep;

class GuideStepTest extends GuideTest
{
    public function test_guide_step_create()
    {
        $user = $this->createAdmin();

        $this->actingAs($user);

        $guide = factory(Guide::class)->create();

        $this->visit(route('resources.guides.steps.create', [$guide->slug]))
            ->type('Step Title', 'title')
            ->type('Description', 'description')
            ->press('Create')
            ->see('Success!');

        $this->seeInDatabase('guide_steps', ['guide_id' => $guide->id]);
    }

    public function test_guide_step_create_and_add_another()
    {
        $user = $this->createAdmin();

        $this->actingAs($user);

        $guide = factory(Guide::class)->create();

        $this->visit(route('resources.guides.steps.create', [$guide->slug]))
            ->type('Step Title', 'title')
            ->type('Description', 'description')
            ->press('Create & Add Another')
            ->see('Success!')
            ->see('Create Step 2');
    }

    public function test_guide_step_store_with_attachment()
    {
        $user = $this->createAdmin();

        $this->actingAs($user);

        $guide = factory(Guide::class)->create();

        $this->visit(route('resources.guides.steps.create', [$guide->slug]))
            ->type('Step Title', 'title')
            ->type('Description', 'description')
            ->attach(base_path('tests/assets/test.jpg'), 'image')
            ->press('Create')
            ->see('Success!');
    }

    public function test_guide_step_store_with_invalid_attachment()
    {
        $user = $this->createAdmin();

        $this->actingAs($user);

        $guide = factory(Guide::class)->create();

        $this->visit(route('resources.guides.steps.create', [$guide->slug]))
            ->type('Step Title', 'title')
            ->type('Description', 'description')
            ->attach(base_path('tests/assets/blank.exe'), 'image')
            ->press('Create')
            ->see('must be an image');
    }

    public function test_delete_guide_step()
    {
        $user = $this->createAdmin();

        $this->actingAs($user);

        $step = factory(GuideStep::class)->create();

        $guide = $step->guide;

        $this->delete(route('resources.guides.steps.destroy', [$guide->slug, $step->id]));

        $this->dontSeeInDatabase('guide_steps', [
            'id' => 1,
        ]);
    }

    public function test_delete_guide_step_image()
    {
        $this->test_guide_step_store_with_attachment();

        $guide = Guide::first();

        $step = $guide->steps()->first();

        $image = $step->images()->first();

        $this->delete(route('resources.guides.steps.images.destroy', [$guide->slug, $step->id, $image->uuid]));

        $this->dontSeeInDatabase('uploads', [
            'id' => 1,
        ]);
    }
}
