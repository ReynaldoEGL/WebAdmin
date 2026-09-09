## Descripción
Este proyecto implementa una API REST para la gestión de tareas
siguiendo el enfoque API-First.

La API fue diseñada mediante un contrato OpenAPI definido en
`openapi.yaml` y posteriormente implementada en el servidor.

El proyecto también conserva las versiones anteriores de la API:

- V1: `/api/v1/`
- V2: `/api/v2/`

La nueva API de gestión de tareas utiliza:

- `GET /tareas`
- `GET /tareas/{id}`
- `POST /tareas`
- `PUT /tareas/{id}`

La implementación también incluye `DELETE /tareas/{id}` como una
extensión adicional.

---

# Cómo levantar el proyecto

El proyecto puede ejecutarse de dos formas:

1. En el servidor de clase.
2. Localmente mediante Docker.

---

# 1. Ejecución en el servidor de clase

La API se encuentra desplegada en el servidor de clase.

La estructura principal de la aplicación se encuentra dentro de:

server/api/api/

El punto de entrada público de la API se encuentra en:

server/api/api/public/

La aplicación utiliza Apache y PHP para procesar las solicitudes y
MySQL/MariaDB para almacenar la información.

Acceso a la API

La API se puede consumir utilizando la URL asignada al servidor.


Los endpoints de la API de tareas se encuentran disponibles mediante:

GET  /tareas
GET  /tareas/{id}
POST /tareas
PUT  /tareas/{id}

El dominio debe sustituirse por la dirección asignada por el servidor
de clase.

Swagger en el servidor

La documentación interactiva se encuentra disponible en:

/server/api/api/public/api-docs/
2. Ejecución local mediante Docker

Para realizar pruebas de manera rápida también se preparó un entorno
local utilizando Docker.

El entorno local utiliza:

Apache + PHP para ejecutar la API.
MariaDB para la base de datos.

La API local utiliza el puerto 8080.

http://localhost:8080
Requisitos

Es necesario tener instalado:

Docker Desktop
Git
Archivos de configuración local

Algunos archivos utilizados exclusivamente para el entorno local no
se encuentran almacenados en el repositorio porque están excluidos
mediante .gitignore.

Estos archivos son:

compose.api-local.yml
docker/api/database.php
docker-data/

Estos archivos contienen configuración específica del entorno local
y no son necesarios para el funcionamiento de la aplicación en el
servidor.

Para ejecutar Docker localmente se deben tener disponibles estos
archivos en la raíz del proyecto.

La estructura local esperada es:

WebAdmin-Server/
├── compose.api-local.yml
├── docker/
│   └── api/
│       ├── Dockerfile
│       ├── vhost.conf
│       └── database.php
├── docker-data/
│   └── init.sql
└── server/
    └── api/
        └── api/

Construir los contenedores
Desde la carpeta raíz del proyecto:

docker compose -f compose.api-local.yml build

Iniciar los servicios
docker compose -f compose.api-local.yml up -d

Verificar los servicios
docker compose -f compose.api-local.yml ps

Deben encontrarse activos los servicios correspondientes a:

webadmin-api
webadmin-db
Acceso local

La API queda disponible en:

http://localhost:8080

Swagger UI:

http://localhost:8080/api-docs/
Detener los servicios
docker compose -f compose.api-local.yml down
Contrato OpenAPI

El contrato de la API se encuentra en:

server/api/api/public/openapi.yaml

El contrato define las operaciones principales de la API de gestión
de tareas.

Endpoints
Listar tareas
GET /tareas
Obtener una tarea
GET /tareas/{id}
Crear una tarea
POST /tareas

Ejemplo de solicitud:

{
    "titulo": "Estudiar API-First",
    "completada": false
}
Actualizar una tarea
PUT /tareas/{id}

Ejemplo:

{
    "titulo": "Estudiar API-First",
    "completada": true
}
Swagger UI

La documentación interactiva utiliza el contrato OpenAPI definido en
openapi.yaml.

Servidor
https://(dominio-vps)/server/api/api/public/api-docs/
Local
http://localhost:8080/api-docs/

Swagger permite visualizar las operaciones disponibles y probar las
peticiones definidas en el contrato.

Evidencia Swagger

![EvidenciaSwagger](docs\images\swagger.png)

Evidencia Postman
![EvidenciaPostman1](docs\images\postman1.png)

![EvidenciaPostman1](docs\images\postman2.png)

![EvidenciaPostman1](docs\images\postman3.png)