# Biodex AI Services

Install the tested dependencies from the repository root:

```powershell
python -m pip install -r .\ai-service\requirements.txt
```

Run each service in a separate terminal from `ai-service`:

```powershell
python .\app.py
python .\waste_ai_service.py
python .\recycling_ai_service.py
```

The services listen on `127.0.0.1` at ports `5000`, `5001`, and `5002`. Configure `FLASK_HOST`, `FLASK_PORT`, `WASTE_AI_PORT`, `RECYCLING_AI_PORT`, or `AI_SERVICE_THREADS` when needed. The default entry points use Waitress; set `FLASK_DEBUG=1` only for local development.

Collection models are stored under `ai-service/models` by default. Set `BIODEX_AI_MODEL_DIR` to move them. Re-train models after upgrading from the legacy `.pkl` format. These local model artifacts use joblib/pickle and must only be loaded from trusted storage.

Collection forecasts use daily volumes, weekly/calendar features, and a chronological validation split. A random forest is retained only when validation error is no worse than the seasonal baseline. Dates absent from the training payload are treated as zero-volume days; responses report that policy and the observed-day coverage. The prediction confidence is a heuristic, not a calibrated probability.

Waste classification is deterministic by default. An optional OpenAI fallback is invoked only when the deterministic classifier marks a waste description unknown or ambiguous. It is disabled unless all three settings are supplied:

```powershell
$env:AI_LLM_ENABLED = "1"
$env:OPENAI_API_KEY = "..."
$env:OPENAI_MODEL = "..."
```

When enabled, the type, category, and description fields for those uncertain requests are sent to the configured OpenAI model. Calls have a short timeout, constrained JSON output, and fall back to manual sorting on errors. LLM labels and heuristic scores are not calibrated confidence probabilities. Price and process recommendations are rule-based estimates; no live market feed or measured efficiency dataset is connected.

Health endpoints are available at `/health` on each service. The recycling service also exposes `/optimize-process`, `/classify-waste`, `/predict-quality`, `/estimate-price`, and `/generate-description`.

Run service tests from the repository root:

```powershell
python -m unittest discover -s ai-service -p "test_*.py" -v
```
