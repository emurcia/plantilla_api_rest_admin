# Seguridad

### Creación de ruta pública

En ocasiones es necesario la creación de rutas públicas o que puedan ser accedidas sin envío de JWT por Headers, sino envió a través del QueryString, para ello es necesario permitir el acceso público de la ruta, para lo cual es necesario editar el archivo `/config/packages/security.yaml` como el ejemplo que se muestra a continuación.

```bash
# codigo ...

access_control:
    - { path: ^/api/v1/ruta, roles: PUBLIC_ACCESS, methods: [GET] }
```

