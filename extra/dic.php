<?php
//Общий концепт DI-контейнеров собранный из нескольких файлов

//Класс хранения DI-зависимостей
class App
{
    private static Container $container;

    public function __construct()
    {
        static::$container = [
            'invokables' => [
                //Другие зависимости
            ],
            'factories'  => [
                ProductHandler::class => ProductHandlerFactory::class,
            ],
        ];
    }

    public function getContainer(): Container {

        return static::$container;
    }
}


//Фабрика создания объекта получаемая из контейнера
class ProductHandlerFactory
{
    public function __invoke(ContainerInterface $container) : RequestHandlerInterface
    {
        $value = $container->get(ProductDepInterface::class);

        return new ProductHandler($value);
    }
}

//Объект внедряемой зависимости
class ProductHandler
{
    private $dep;
    public function __construct(ProductDepInterface $value) 
    {
        $this->value = $value;
    }
    public function __invoke(ServerRequestInterface $request): RequestHandlerInterface
    {
         //
    }
}