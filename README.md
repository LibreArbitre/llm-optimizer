# 🎯 LLM Optimizer

> **Find the optimal LLM configuration for your GPU's VRAM**

A lightweight web application that helps you determine the best Large Language Model, quantization level, and context size based on your available GPU memory.

[![Docker Build](https://github.com/YOUR_USERNAME/llm-optimizer/workflows/Docker%20Build%20%26%20Test/badge.svg)](https://github.com/YOUR_USERNAME/llm-optimizer/actions)
[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

## 🌟 Features

### 🎮 GPU Presets
Quick selection for popular GPUs:
- Consumer GPUs: RTX 4060, 4070, 4070 Ti, 5070 Ti, 4090
- Data Center GPUs: L4, A10, L40S, H100, A100

### 🤖 Supported Models
- **Large**: Llama 3.3 70B, Llama 3.1 70B, Mixtral 8x7B
- **Medium**: Qwen 2.5 32B/14B
- **Small**: Llama 3.1 8B, Mistral 7B, Gemma 2 9B
- **Tiny**: Phi-3 Mini 3.8B, Gemma 2 2B

### ⚙️ Quantization Support
- **FP16**: Maximum precision
- **FP8**: Recommended - good quality/performance balance
- **FP4**: Maximum VRAM savings

### 🎯 Optimization Modes
- **Balanced**: Best overall compromise
- **Largest Model**: Prioritizes model parameter count
- **Maximum Context**: Optimizes for longest context window
- **Best Quality**: Minimizes quantization

### 🌐 Multi-language
- English (default)
- French
- Language preference saved in cookies

## 🚀 Quick Start

### Using Docker Compose (Recommended)

```bash
git clone https://github.com/YOUR_USERNAME/llm-optimizer.git
cd llm-optimizer
docker-compose up -d
```

Access at: `http://localhost:8080`

### Using Docker

```bash
docker build -t llm-optimizer .
docker run -d -p 8080:80 llm-optimizer
```

### Without Docker

Requirements: PHP 7.4+

```bash
php -S 0.0.0.0:8080
```

## 📐 How It Works

### Calculation Formula

```
Total VRAM = (Parameters × Precision Factor) + (Context Size × 0.0005)
```

**Precision Factors:**
- FP16: 2
- FP8: 1
- FP4: 0.5

### Example Calculations

**Llama 3.1 8B in FP8 with 32K context:**
- Model: 8B × 1 = 8 GB
- Context: 32,768 × 0.0005 = 16.4 GB
- **Total: 24.4 GB** → Requires 1× RTX 4090 (24GB)

**Qwen 2.5 32B in FP16 with 16K context:**
- Model: 32B × 2 = 64 GB
- Context: 16,384 × 0.0005 = 8.2 GB
- **Total: 72.2 GB** → Requires 1× A100 (80GB)

### Algorithm

1. For each model and quantization level:
   - Calculate model memory: `params × precision_factor`
   - Calculate available context memory: `(vram × 0.95) - model_memory`
   - Find maximum context: `context_memory / 0.0005`
   - Validate against minimum context constraint

2. Score configurations based on priority:
   - **Balanced**: `(params × 100) + (context / 100) - (precision × 50)`
   - **Model**: `(params × 1000) - (precision × 100) + (context / 1000)`
   - **Context**: `(context × 1000) + params`
   - **Quality**: `((3 - precision) × 10000) + (params × 100) + (context / 1000)`

3. Return top 3 + additional viable configurations

## 🏗️ Architecture

- **Backend**: PHP 8.2-FPM
- **Web Server**: Nginx (Alpine)
- **Base Image**: Alpine Linux
- **Image Size**: ~50MB
- **Memory Usage**: ~10MB RAM
- **Response Time**: <100ms

### Project Structure

```
llm-optimizer/
├── index.php              # Main application
├── Dockerfile             # Container definition
├── docker-compose.yml     # Local development
├── nginx.conf            # Web server config
├── start.sh              # Startup script
├── .github/
│   └── workflows/
│       └── docker-build.yml  # CI/CD pipeline
├── .gitignore
├── .dockerignore
└── README.md
```

## 🧪 Testing

Run the automated test suite:

```bash
# Build and test
docker build -t llm-optimizer:test .
docker run -d -p 8888:80 --name test llm-optimizer:test

# Health check
curl http://localhost:8888

# Cleanup
docker stop test && docker rm test
```

GitHub Actions automatically runs tests on every push.

## 🌐 Deployment

### General Requirements

- Docker support
- Port 80 available (or custom port mapping)
- Minimal resources: 128MB RAM, 0.1 CPU

### Platform-Specific Guides

<details>
<summary><b>Docker Swarm</b></summary>

```bash
docker service create \
  --name llm-optimizer \
  --publish 80:80 \
  --replicas 2 \
  llm-optimizer:latest
```
</details>

<details>
<summary><b>Kubernetes</b></summary>

```yaml
apiVersion: apps/v1
kind: Deployment
metadata:
  name: llm-optimizer
spec:
  replicas: 2
  selector:
    matchLabels:
      app: llm-optimizer
  template:
    metadata:
      labels:
        app: llm-optimizer
    spec:
      containers:
      - name: llm-optimizer
        image: llm-optimizer:latest
        ports:
        - containerPort: 80
---
apiVersion: v1
kind: Service
metadata:
  name: llm-optimizer
spec:
  selector:
    app: llm-optimizer
  ports:
  - port: 80
    targetPort: 80
  type: LoadBalancer
```
</details>

<details>
<summary><b>Docker Compose Production</b></summary>

```yaml
version: '3.8'
services:
  app:
    image: llm-optimizer:latest
    restart: always
    ports:
      - "80:80"
    deploy:
      resources:
        limits:
          memory: 256M
          cpus: '0.5'
```
</details>

### Reverse Proxy

The application works behind any reverse proxy (Traefik, Nginx, Caddy). It listens on port 80 and supports health checks at `/`.

## 🔧 Configuration

### Environment Variables

None required. The application is stateless and requires no configuration.

### Custom Models

To add your own models, edit `index.php`:

```php
$models = [
    ['name' => 'Your Model', 'params' => 13, 'category' => 'Medium'],
    // Add more...
];
```

### Custom Port

In `docker-compose.yml`:
```yaml
ports:
  - "YOUR_PORT:80"
```

## 📊 Use Cases

### Example 1: RTX 5070 Ti Owner (16GB)
**Question**: "What can I run with decent context?"

**Results**:
- ⭐ Llama 3.1 8B (FP8) → 128K context
- ✓ Mistral 7B (FP8) → 128K context
- ✓ Qwen 2.5 14B (FP4) → 32K context

### Example 2: Data Center Deployment (A100 80GB)
**Question**: "Largest model with 32K+ context?"

**Results**:
- ⭐ Llama 3.3 70B (FP16) → 32K context
- ✓ Llama 3.3 70B (FP8) → 128K context (recommended)

### Example 3: Maximum Context Priority
**Question**: "Longest possible context window?"

**Results**:
- ⭐ Gemma 2 2B (FP8) → 256K context
- ✓ Phi-3 Mini 3.8B (FP8) → 256K context

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

### Development Setup

```bash
git clone https://github.com/YOUR_USERNAME/llm-optimizer.git
cd llm-optimizer
docker-compose up
```

### Adding New Models

1. Edit the `$models` array in `index.php`
2. Test locally
3. Submit PR with model name, parameter count, and category

### Adding Languages

1. Add translation array in `index.php`
2. Add language selector button
3. Test all pages

## 📝 License

MIT License - feel free to use this project for any purpose.

## 🙏 Credits

Based on GPU calculation formulas from [OVHcloud's LLM GPU Guide](https://blog.ovhcloud.com/gpu-for-llm-inferencing-guide/).

## 📧 Support

- 🐛 **Issues**: [GitHub Issues](https://github.com/YOUR_USERNAME/llm-optimizer/issues)
- 💬 **Discussions**: [GitHub Discussions](https://github.com/YOUR_USERNAME/llm-optimizer/discussions)

---

**Made with ❤️ for the LLM community**
