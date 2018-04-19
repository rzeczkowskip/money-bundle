<?php

namespace JK\MoneyBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * This class that loads and manages bundle configuration.
 * @author Jakub Kucharovic <jakub@kucharovic.cz>
 */
class JKMoneyExtension extends Extension implements PrependExtensionInterface
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $locale        = $container->getParameter('kernel.default_locale');
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.xml');

        if (interface_exists('Symfony\Component\Form\FormInterface')) {
            $formType = $container->getDefinition('JK\MoneyBundle\Form\Type\MoneyType');
            $formType->replaceArgument(0, $config['currency']);
        }

        if (interface_exists('Twig_ExtensionInterface')) {
            $twigExtension = $container->getDefinition('JK\MoneyBundle\Twig\MoneyExtension');
            $twigExtension->replaceArgument(0, $locale);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function prepend(ContainerBuilder $container)
    {
        $container->prependExtensionConfig('doctrine', [
            'orm' => [
                'mappings' => [
                    'JKMoneyBundle' => [
                        'type' => 'xml',
                        'prefix' => 'Money'
                    ]
                ]
            ]
        ]);
    }
}
