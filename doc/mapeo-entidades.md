# Mapeo de entidad desde la base de datos

Los siguientes comandos están basados en la [documentación oficial de symfony](https://symfony.com/doc/current/doctrine/reverse_engineering.html)

### Mapeo de toda la base

Para mapear las tablas que se encuentran en la base de datos a entidades del aplicativo se debe de ejcutar los siguientes comandos


```bash
# Generacion de entidad
php bin/console doctrine:mapping:import 'App\Entity' annotation --path=src/Entity
# Generación de getter y setters
php bin/console make:entity --regenerate App
# En caso de utilizar docker será necesario cambiar los permisos de los archivos
sudo chown usuario:usuario src/Entity/*
```



### Mapeo de entidad específica

```bash
# Generacion de entidad
php bin/console doctrine:mapping:import 'App\Entity' annotation --path=src/Entity --filter='CtlTabla'
# Generación de getter y setters
php bin/console make:entity --regenerate App
# En caso de utilizar docker será necesario cambiar los permisos de los archivos
sudo chown usuario:usuario src/Entity/*
```

