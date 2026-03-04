# IDN-CyberRange

<div align="center">

**Platform lab keamanan siber berbasis Docker untuk keperluan edukasi dan pelatihan enterprise.**

[![Docker](https://img.shields.io/badge/Docker-Single%20Container-2496ED?logo=docker&logoColor=white)](https://docker.com)
[![PHP](https://img.shields.io/badge/PHP-8.1-777BB4?logo=php&logoColor=white)](https://php.net)
[![MySQL](https://img.shields.io/badge/MySQL-8.0-4479A1?logo=mysql&logoColor=white)](https://mysql.com)
[![Platform](https://img.shields.io/badge/Platform-Ubuntu%2020.04+-E95420?logo=ubuntu&logoColor=white)](https://ubuntu.com)
[![License](https://img.shields.io/badge/Use-Educational%20Only-e63946)](#disclaimer)

</div>

---

## Daftar Lab

| Lab | Topik | Port | Tingkat | Status |
|-----|-------|------|---------|--------|
| [XML Injection](./lab-xml/) | XML Injection — Basic XML, XPath, XXE | `8081` | Easy–Hard | Tersedia |

---

## Prasyarat

- Ubuntu 20.04 / Debian 11+ (fisik, VM, atau VPS)
- Docker Engine terinstall

```bash
# Install Docker
curl -fsSL https://get.docker.com | sh
sudo usermod -aG docker $USER
newgrp docker
```

---

## Cara Menjalankan

Setiap lab berjalan secara independen dalam container terpisah. Keduanya dapat dijalankan bersamaan karena menggunakan port yang berbeda.

```bash
# Clone repository
git clone https://github.com/15K4R10T/XMLInjection-Machine-CyberRange-IDN.git
cd IDN-CyberRange

# Jalankan Lab XML Injection (port 8081)
cd ../lab-xml
chmod +x run.sh && ./run.sh
```

Akses via browser:

```
http://<IP-VM>:8081    →  XML Injection Lab
```

---

## Struktur Repository

```
IDN-CyberRange/
├── README.md
└── lab-xml/                  XML Injection Lab
    ├── Dockerfile
    ├── run.sh                Build & deploy (port 8081)
    ├── init.sql
    ├── entrypoint.sh
    ├── supervisord.conf
    ├── apache.conf
    └── web/
        ├── index.php         Dashboard
        ├── basic/            Modul 1 - Basic XML Injection
        ├── xpath/            Modul 2 - XPath Injection
        ├── xxe/              Modul 3 - XXE Injection
        └── includes/
```

---

## Manajemen Container

```bash
# Lihat semua container yang berjalan
docker ps

# Lihat log real-time
docker logs -f lab-xml

# Stop semua lab
docker stop lab-xml

# Start ulang semua lab
docker start lab-xml

# Hapus dan rebuild dari awal
docker rm -f lab-xml  && cd lab-xml          && ./run.sh
```

---

## Disclaimer

> Seluruh lab dalam repositori ini dibuat **hanya untuk keperluan edukasi dan pelatihan keamanan siber** di lingkungan yang terisolasi.
> Jangan gunakan teknik yang dipelajari pada sistem, jaringan, atau aplikasi tanpa izin tertulis dari pemiliknya.
> ID-Networkers tidak bertanggung jawab atas segala bentuk penyalahgunaan materi dalam repositori ini.

---

<div align="center">
  <sub>Dibuat oleh <strong>ID-Networkers</strong> — Indonesian IT Expert Factory</sub>
</div>
