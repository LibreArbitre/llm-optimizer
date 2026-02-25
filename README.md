# 🎯 LLM Optimizer

> **Find the optimal LLM configuration for your GPU's VRAM**

A lightweight web application that helps you determine the best Large Language Model, quantization level, and context size based on your available GPU memory.

[![License: MIT](https://img.shields.io/badge/License-MIT-yellow.svg)](https://opensource.org/licenses/MIT)

## 🌟 Features

### 🎮 GPU Presets
Quick selection for popular GPUs:
- **Consumer NVIDIA RTX 50 series**: RTX 5060 (8 GB), RTX 5070 (12 GB), RTX 5070 Ti (16 GB), RTX 5080 (24 GB), RTX 5090 (32 GB)
- **Consumer NVIDIA RTX 40 series**: RTX 4080 (16 GB), RTX 4090 (24 GB)
- **Data Center NVIDIA**: A100 40/80 GB, L40S 48 GB, H100 80 GB, H200 94/141 GB, B100 192 GB, B200 288 GB
- **Data Center AMD**: MI300X 192 GB, MI325X 256 GB

### 🤖 Supported Models (February 2026)

| Category | Models |
|----------|--------|
| **Tiny (< 5B)** | Llama 3.2 1B/3B, Gemma 3 1B/4B, Phi-4 Mini 3.8B, Qwen3 0.6B/1.7B/4B, Qwen2.5 3B |
| **Small (5–15B)** | Gemma 3 12B, Mistral Nemo 12B, Qwen3 8B, Qwen2.5 7B/14B, Phi-4 14B |
| **Coding** | Qwen2.5 Coder 7B/32B, Granite 3.3 8B, Codestral 22B |
| **Reasoning** | DeepSeek-R1 Distill 7B/8B/14B/32B/70B, DeepSeek-R1 671B |
| **Vision** | Llama 3.2 11B/90B Vision, Qwen2.5-VL 3B/7B/32B/72B, Pixtral 12B, Qwen3-VL 32B/235B |
| **Medium (15–50B)** | Gemma 3 27B, Mistral Small 3.1 24B, Qwen3 14B/32B/30B (MoE), Qwen2.5 32B |
| **Large (50–150B)** | Llama 3.3 70B, Qwen2.5 72B, Llama 4 Scout (MoE 17B active) |
| **Huge (150B+)** | Qwen3 235B (MoE), Llama 4 Maverick (MoE), DeepSeek-V3 671B (MoE), Qwen3-Coder 480B (MoE), Kimi K2 1T (MoE) |

### ⚙️ Quantization Support
- **FP16**: Maximum precision (2 bytes/param)
- **FP8**: Good quality/performance balance (1 byte/param)
- **FP4**: Maximum VRAM savings (0.5 bytes/param)

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
Total VRAM = (Parameters × Precision Factor) + (Context Size × KV_per_token)
```

**Precision Factors:**
- FP16: 2
- FP8: 1
- FP4: 0.5

**KV Cache per token** (scales with model size via GQA):
```
kv_per_token = max(0.08, 0.04 × √params_B)  MB/token
```

This sqrt scaling reflects that modern models use Group Query Attention (GQA), where the number of KV heads grows much slower than total parameters. Calibrated values:

| Model size | KV cache/token |
|-----------|---------------|
| 8B  | ~0.11 MB |
| 14B | ~0.15 MB |
| 32B | ~0.23 MB |
| 70B | ~0.33 MB |

### Example Calculations

**Qwen2.5 14B in FP4 with 32K context on 16 GB GPU:**
- Model: 14B × 0.5 = 7.0 GB
- KV cache: 32,768 × 0.00015 = 4.9 GB
- **Total: 11.9 GB** ✓ fits in 16 GB (74%)

**Llama 3.3 70B in FP4 on A100 80GB:**
- Model: 70B × 0.5 = 35 GB
- Remaining for context: ~41 GB → up to 64K–128K context

### Algorithm

1. For each model and quantization level:
   - Calculate model memory: `params × precision_factor`
   - Calculate available context memory: `(vram × 0.95) - model_memory`
   - Compute KV cost: `max(0.00008, 0.00004 × √params)` GB/token
   - Find maximum context: `context_memory / kv_per_token`
   - Validate against minimum context constraint

2. Score configurations based on priority:
   - **Balanced**: `(params × 100) + (context / 100) - (precision × 50)`
   - **Model**: `(params × 1000) - (precision × 100) + (context / 1000)`
   - **Context**: `(context × 1000) + params`
   - **Quality**: `((3 - precision) × 10000) + (params × 100) + (context / 1000)`

3. Return top 3 diversified recommendations + additional viable configurations

## 🏗️ Architecture

- **Backend**: PHP 8.2-FPM
- **Web Server**: Nginx (Alpine)
- **Base Image**: Alpine Linux
- **Image Size**: ~50 MB
- **Memory Usage**: ~10 MB RAM
- **Response Time**: <100 ms

### Project Structure

```
llm-optimizer/
├── index.php              # Main application
├── Dockerfile             # Container definition
├── docker-compose.yml     # Local development
├── nginx.conf             # Web server config
├── start.sh               # Startup script
└── README.md
```

## 🧪 Testing

```bash
# Build and test
docker build -t llm-optimizer:test .
docker run -d -p 8888:80 --name test llm-optimizer:test

# Health check
curl http://localhost:8888

# Cleanup
docker stop test && docker rm test
```

## 🌐 Deployment

### General Requirements

- Docker support
- Port 80 available (or custom port mapping)
- Minimal resources: 128 MB RAM, 0.1 CPU

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
    ['name' => 'Your Model', 'params' => 13, 'tags' => ['code']],
    // tags: 'code', 'vision', 'reasoning', 'multilingual'
];
```

### Custom Port

In `docker-compose.yml`:
```yaml
ports:
  - "YOUR_PORT:80"
```

## 📊 Use Cases

### Example 1: RTX 5070 Ti Owner (16 GB)
**Question**: "What can I run with decent context?"

**Results**:
- ⭐ Qwen2.5 14B (FP4) → 32K context
- ✓ Gemma 3 12B (FP8) → 64K context
- ✓ Qwen3 8B (FP4) → 64K context

### Example 2: Data Center Deployment (A100 80 GB)
**Question**: "Largest model with 32K+ context?"

**Results**:
- ⭐ Llama 3.3 70B (FP4) → 64K context
- ✓ Qwen2.5 72B (FP4) → 64K context

### Example 3: Maximum Context Priority
**Question**: "Longest possible context window on 16 GB?"

**Results**:
- ⭐ Qwen3 1.7B (FP4) → 512K context
- ✓ Llama 3.2 3B (FP4) → 512K context

## 🤝 Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

### Adding New Models

1. Edit the `$models` array in `index.php`
2. Test locally
3. Submit PR with model name, parameter count, and tags

### Adding Languages

1. Add translation array in `index.php`
2. Add language selector button
3. Test all pages

## 📝 License

MIT License - feel free to use this project for any purpose.

## 📧 Support

- 🐛 **Issues**: [GitHub Issues](https://github.com/YOUR_USERNAME/llm-optimizer/issues)
- 💬 **Discussions**: [GitHub Discussions](https://github.com/YOUR_USERNAME/llm-optimizer/discussions)

---

**Made with ❤️ for the LLM community**
