<?php

namespace App\Form;

use App\Entity\Post;
use App\Form\Type\DateTimePickerType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\String\Slugger\SluggerInterface;

class PostType extends AbstractType
{
    public function __construct(
        private SluggerInterface $slugger
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, [
                'attr' => [
                    'autofocus' => true,
                    'class' => 'block p-2.5 w-full text-gray-900 bg-gray-50 rounded-lg border border-gray-300 sm:text-md focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500',
                ],
                'label' => 'Title',
                'label_attr' => [
                    'class' => 'block mb-2 text-sm font-medium text-gray-900 dark:text-gray-300',
                ],
            ])
            ->add('summary', TextareaType::class, [
                'attr' => [
                    'autofocus' => true,
                    'rows' => 4,
                    'class' => 'block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500',
                ],
                'label' => 'Summary',
                'label_attr' => [
                    'class' => 'block mb-2 text-sm font-medium text-gray-900 dark:text-gray-400',
                ],
            ])
            ->add('content', TextareaType::class, [
                'attr' => [
                    'rows' => 20,
                    'class' => 'block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500',
                ],
                'label' => 'Content',
                'label_attr' => [
                    'class' => 'block mb-2 text-sm font-medium text-gray-900 dark:text-gray-400',
                ],
            ])
            ->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
                /** @var Post $post */
                $post = $event->getData();
                $post->setPublishedAt(new \DateTime());

                if (null === $post->getSlug() && null !== $post->getTitle()) {
                    $post->setSlug($this->slugger->slug($post->getTitle())->lower());
                }
            })
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
