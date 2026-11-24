FROM mirror.gcr.io/library/python:3.11-slim
RUN apt-get update && apt-get install -y --no-install-recommends docker-cli \
    && rm -rf /var/lib/apt/lists/*
RUN pip install --no-cache-dir fastapi uvicorn pymysql pydantic python-multipart
WORKDIR /srv
COPY app/ ./app/
COPY static/ ./static/
CMD ["uvicorn", "app.main:app", "--host", "0.0.0.0", "--port", "8000"]
