# docker-engine-api

[![Dockerhub](https://images.microbadger.com/badges/version/luizeof/docker-engine-api.svg)](https://microbadger.com/images/luizeof/docker-engine-api "Get your own version badge on microbadger.com") [![Dockerhub](https://images.microbadger.com/badges/image/luizeof/docker-engine-api.svg)](https://microbadger.com/images/luizeof/docker-engine-api "Get your own image badge on microbadger.com")

Docker Engine API Wrapper with Basic Authentication

[https://hub.docker.com/r/luizeof/docker-engine-api](https://hub.docker.com/r/luizeof/docker-engine-api)

---

To access the Docker Engine API over HTTP with Basic Authentication, just run this container:

```yaml
docker_engine_api:
  container_name: docker_engine_api
  image: luizeof/docker-engine-api
  privileged: true
  environment:
    - AUTHUSER=myuser
    - AUTHPWD=mypwd
  ports:
    - "8099:80"
  restart: always
  volumes:
    - /var/run/docker.sock:/var/run/docker.sock
```

## Environments

There are 2 variables that you need to configure:

### AUTHUSER

The username for basic authentication.

### AUTHPWD

The password for basic authentication.

## Usage


