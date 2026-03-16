# Local Docker proxy setup for development under .docker domain

This configuration requires local DNS (e.g. Dnsmasq) to be installed to properly route custom domain to Docker containers.

## Install local DNS - Dnsmasq

```
Install dnsmasq (via Homebrew):
brew install dnsmasq
```

### Configure dnsmasq to resolve `.docker` domains to localhost:
```
echo "address=/.docker/127.0.0.1" > $(brew --prefix)/etc/dnsmasq.conf
```

### Set up macOS to use dnsmasq for `.docker` domains:
```
sudo mkdir -p /etc/resolver
echo "nameserver 127.0.0.1" | sudo tee /etc/resolver/docker
```

### Start dnsmasq:
```
sudo brew services start dnsmasq
```

### Test it:
```
ping website.docker
```
This should resolve to `127.0.0.1`.



## Launch proxy docker image

Create virtual network in docker where all machines will communicate:  
```
docker network create proxy-dev-net
```

Run the proxy docker image:  
```
docker compose up -d
```
