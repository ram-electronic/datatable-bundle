<?php

declare(strict_types=1);

namespace RamElectronic\DataTableBundle\Tests\Integration;

use RamElectronic\DataTableBundle\DataTableBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\TwigBundle\TwigBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\UX\Icons\UXIconsBundle;
use Symfony\UX\LiveComponent\LiveComponentBundle;
use Symfony\UX\StimulusBundle\StimulusBundle;
use Symfony\UX\TwigComponent\TwigComponentBundle;
use TalesFromADev\Twig\Extra\Tailwind\Bridge\Symfony\Bundle\TalesFromADevTwigExtraTailwindBundle;
use Twig\Extra\TwigExtraBundle\TwigExtraBundle;

final class Kernel extends BaseKernel
{
    /**
     * @return iterable<BundleInterface>
     */
    #[\Override]
    public function registerBundles(): iterable
    {
        yield new FrameworkBundle();
        yield new TwigBundle();
        yield new TwigComponentBundle();
        yield new StimulusBundle();
        yield new LiveComponentBundle();
        yield new UXIconsBundle();
        yield new TwigExtraBundle();
        yield new TalesFromADevTwigExtraTailwindBundle();
        yield new DataTableBundle();
    }

    #[\Override]
    public function registerContainerConfiguration(LoaderInterface $loader): void
    {
        $loader->load(function (ContainerBuilder $container): void {
            $container->loadFromExtension('framework', [
                'secret' => 'test',
                'test' => true,
                'http_method_override' => false,
                'router' => [
                    'utf8' => true,
                    'resource' => __DIR__.'/routes.php',
                    'type' => 'php',
                ],
                'form' => true,
                'csrf_protection' => true,
                'session' => ['storage_factory_id' => 'session.storage.factory.mock_file'],
                'validation' => ['enabled' => true],
                'translator' => ['enabled' => true, 'default_path' => '%kernel.project_dir%/translations'],
                'default_locale' => 'en',
            ]);

            $container->loadFromExtension('twig', [
                'default_path' => '%kernel.project_dir%/tests/Integration/templates',
            ]);

            $container->loadFromExtension('twig_component', [
                'anonymous_template_directory' => 'components',
                'defaults' => [
                    'RamElectronic\\DataTableBundle\\Presentation\\Twig\\Components\\' => '@DataTable/components/',
                ],
            ]);
        });
    }

    #[\Override]
    public function getProjectDir(): string
    {
        return \dirname(__DIR__, 2);
    }

    #[\Override]
    public function getCacheDir(): string
    {
        return sys_get_temp_dir().'/datatable-bundle-tests/cache';
    }

    #[\Override]
    public function getLogDir(): string
    {
        return sys_get_temp_dir().'/datatable-bundle-tests/log';
    }
}
