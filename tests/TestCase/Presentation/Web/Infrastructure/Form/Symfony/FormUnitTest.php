<?php

declare(strict_types=1);

/*
 * This file is part of the Explicit Architecture POC,
 * which is created on top of the Symfony Demo application.
 *
 * (c) Herberto Graça <herberto.graca@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Acme\App\Test\TestCase\Presentation\Web\Infrastructure\Form\Symfony;

use Acme\App\Presentation\Web\Infrastructure\Form\Symfony\Form;
use Acme\App\Test\Framework\AbstractUnitTest;
use Mockery;
use Mockery\MockInterface;
use Psr\Http\Message\ServerRequestInterface;
use stdClass;
use Symfony\Bridge\PsrHttpMessage\HttpFoundationFactoryInterface;
use Symfony\Component\Form\Form as SymfonyForm;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\Request;

/**
 * THIS IS HERE AS AN EXAMPLE OF A BAD TEST.
 * DON'T MOCK WHAT YOU DON'T OWN (from the book "Growing Object Oriented Software, Guided by Tests")
 * The unit under test is an adapter, which is by definition purely for integration so this should be tested with an
 *  integration test.
 * Regardless of my previous "decree", it is more important that we take a look at the test and understand why it's
 *  not a good test:
 *      It is mocking the integration target, it is assuming (mocking) the behaviour of the underlying symfony form,
 *  and if that behaviour is not correct because of a bug, or simply because it changes, this test should break but
 *  it won't (because we are mocking the integration target).
 * So it is not a reliable test.
 * It is testing the implementation and not the actual functionality: We should be able to test another implementation
 *  of the same functionality with fundamentally the same test but we can't, we would need to mock all different
 *  method calls in every test.
 *
 * @small
 *
 * @internal
 */
final class FormUnitTest extends AbstractUnitTest
{
    /**
     * @var Form
     */
    private $form;

    /**
     * @var MockInterface|SymfonyForm
     */
    private $symfonyForm;

    /**
     * @var MockInterface|HttpFoundationFactoryInterface
     */
    private $symfonyResponseFactory;

    protected function setUp(): void
    {
        $this->symfonyForm = Mockery::mock(SymfonyForm::class);
        $this->symfonyResponseFactory = Mockery::mock(HttpFoundationFactoryInterface::class);

        $this->form = new Form(
            $this->symfonyResponseFactory,
            $this->symfonyForm
        );
    }

    /**
     * @test
     */
    public function create_view(): void
    {
        $this->symfonyForm->shouldReceive('createView')->once()->andReturn($formView = Mockery::mock(FormView::class));

        self::assertSame($formView, $this->form->createView());
    }

    /**
     * @test
     */
    public function get_data(): void
    {
        $this->symfonyForm->shouldReceive('getData')->once()->andReturn($expectedReturn = new stdClass());

        self::assertSame($expectedReturn, $this->form->getData());
    }

    /**
     * @test
     */
    public function handle_request(): void
    {
        $psrRequestMock = Mockery::mock(ServerRequestInterface::class);
        $this->symfonyResponseFactory->shouldReceive('createRequest')
            ->once()
            ->with($psrRequestMock)
            ->andReturn($symfonyRequestMock = Mockery::mock(Request::class));
        $this->symfonyForm->shouldReceive('handleRequest')
            ->once()
            ->with($symfonyRequestMock);

        $this->form->handleRequest($psrRequestMock);
    }

    /**
     * @test
     * @dataProvider provideFormIsSubmittedIsValid
     */
    public function should_be_processed(bool $isSubmitted, bool $isValid, bool $expectedResult): void
    {
        $this->symfonyForm->shouldReceive('isSubmitted')->once()->andReturn($isSubmitted);
        if ($isSubmitted) {
            $this->symfonyForm->shouldReceive('isValid')->once()->andReturn($isValid);
        }

        self::assertSame($expectedResult, $this->form->shouldBeProcessed());
    }

    public function provideFormIsSubmittedIsValid(): array
    {
        return [
            [false, false, false],
            [true, false, false],
            [false, true, false],
            [true, true, true],
        ];
    }

    /**
     * @test
     * @dataProvider provideClickedButton
     */
    public function clicked_button(bool $has, ?bool $wasClicked, bool $expectedResult): void
    {
        $buttonName = 'something';

        $this->symfonyForm->shouldReceive('has')->once()->with($buttonName)->andReturn($has);
        if ($wasClicked !== null) {
            $button = Mockery::mock(FormInterface::class);
            $button->shouldReceive('isClicked')->once()->andReturn($wasClicked);
            $this->symfonyForm->shouldReceive('get')->once()->with($buttonName)->andReturn($button);
        }

        self::assertSame($expectedResult, $this->form->clickedButton($buttonName));
    }

    public function provideClickedButton(): array
    {
        return [
            [false, null, false],
            [true, false, false],
            [true, true, true],
        ];
    }

    /**
     * @test
     */
    public function get_form_name(): void
    {
        $formName = 'something';
        $this->symfonyForm->shouldReceive('getName')->once()->andReturn($formName);

        self::assertSame($formName, $this->form->getFormName());
    }
}
