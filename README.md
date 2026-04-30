# Research_proposal_writing
# Evaluating the Robustness of LLMs Against Prompt Injection Attacks

**Author:** Abdurehim Mohammed  
**Course:** Research Methods in Computer Science (Hawassa University)  
**Domain:** Artificial Intelligence / Cybersecurity  

---

## 1. Proposal Review & Overview
This repository contains the architecture and implementation guidelines for the Simulated Customer Service Evaluation Environment (SCSEE). The foundational research proposal addresses a critical security gap in modern AI deployments: while Large Language Models (LLMs) are rapidly replacing traditional customer service agents, they lack the architectural safeguards necessary to distinguish between trusted system instructions and malicious user inputs. 

The proposal systematically outlines a methodology to benchmark four leading models (GPT-4o, Claude 3.5 Sonnet, LLAMA 3 8B, and Mistral 7B) using a custom suite of 120+ domain-specific injection test cases. Furthermore, it introduces a layered defense strategy (combining input sanitization, instruction hierarchy, and BERT-based output guardrails) designed to mitigate these vulnerabilities without breaking the core functionality of the service. 

## 2. Abstract
The emergence of Large Language Models (LLMs) has revolutionized automated customer service, enabling efficient handling of sensitive queries and transactions. However, this rapid adoption has outpaced the development of necessary security frameworks, making these systems highly susceptible to prompt injection attacks. Traditional cybersecurity defenses are ill-equipped to handle threats embedded within grammatically correct natural language. 

This project introduces a domain-specific adversarial evaluation framework to systematically assess LLM robustness against prompt injection in customer service environments. By deploying a Simulated Customer Service Evaluation Environment (SCSEE), the research evaluates attack success across multiple models and tests a layered defense architecture. The primary goal is to measurably reduce the Attack Success Rate (ASR) while maintaining a commercially viable False Positive Rate (FPR) of under 8%, ultimately ensuring safer AI deployments in sensitive business contexts.

---

## 3. System Architecture (SCSEE)
The environment is structured into a four-layer modular platform[cite: 1]:
* **Customer Interface Layer:** A browser-based chat portal built with React.js to replicate user-facing input surfaces[cite: 1].
* **Orchestration Middleware Layer:** The core control plane built with Python and FastAPI to manage LLM routing and enforce configurations[cite: 1].
* **LLM Integration Layer:** Standardized API connections to OpenAI, Anthropic, and local Ollama inference models[cite: 1].
* **Logging and Analysis Layer:** Event storage and metric computation utilizing PostgreSQL and Jupyter[cite: 1].

---

## 4. Installation & Setup Instructions

### Prerequisites
Ensure you have the following installed on your system:
* Python 3.11 or higher[cite: 1]
* Node.js and npm (for the frontend React application)[cite: 1]
* PostgreSQL (for the logging database)[cite: 1]
* Git

### Step 1: Clone the Repository
```bash
git clone [https://github.com/yourusername/Research_proposal_writing.git](https://github.com/yourusername/Research_proposal_writing.git)
cd Research_proposal_writing
Step 2: Backend Setup (FastAPI & Python)
Navigate to the backend directory and install the required ML and API libraries[cite: 1].
cd scsee-backend

# Create a virtual environment
python -m venv venv
source venv/bin/activate  # On Windows use: venv\Scripts\activate

# Install core dependencies
pip install fastapi uvicorn sentence-transformers transformers scikit-learn faker
