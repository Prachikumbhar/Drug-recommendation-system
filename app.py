from fastapi import FastAPI
from fastapi.middleware.cors import CORSMiddleware
from pydantic import BaseModel
import pandas as pd
from sklearn.feature_extraction.text import TfidfVectorizer
from sklearn.metrics.pairwise import cosine_similarity
import os

app = FastAPI()


app.add_middleware(
    CORSMiddleware,
    allow_origins=["*"],  
    allow_credentials=True,
    allow_methods=["GET", "POST", "OPTIONS"],
    allow_headers=["*"],
)

MEDICINE_DATA_FILE = "allopathy3.csv"
REMEDY_DATA_FILE = "Ayurvedic2.csv"

if not os.path.exists(MEDICINE_DATA_FILE) or not os.path.exists(REMEDY_DATA_FILE):
    raise FileNotFoundError("CSV files not found!")


medicine_data = pd.read_csv(MEDICINE_DATA_FILE).dropna(subset=["Medicines", "Symptoms", "Disease", "Ratings"])
remedy_data = pd.read_csv(REMEDY_DATA_FILE).dropna(subset=["Disease", "Remedy"])


medicine_vectorizer = TfidfVectorizer(stop_words="english", ngram_range=(1, 2))
medicine_matrix = medicine_vectorizer.fit_transform(medicine_data["Symptoms"])

remedy_vectorizer = TfidfVectorizer(stop_words="english")
remedy_matrix = remedy_vectorizer.fit_transform(remedy_data["Disease"])


class RecommendationRequest(BaseModel):
    input_symptoms: str
    threshold: float = 0.5  

class RecommendationResponse(BaseModel):
    input_symptoms: str
    recommended_medicines: list = []
    recommended_remedies: list = []


def recommend_medicines(input_symptoms: str, threshold: float):
    input_vector = medicine_vectorizer.transform([input_symptoms])
    scores = cosine_similarity(input_vector, medicine_matrix).flatten()

    results = []
    for idx, score in enumerate(scores):
        if score >= threshold:
            results.append({
                "disease": medicine_data.iloc[idx]["Disease"],
                "recommended_medicine": medicine_data.iloc[idx]["Medicines"],
                "rating": medicine_data.iloc[idx]["Ratings"],
                "similarity": round(score, 2)
            })
    return sorted(results, key=lambda x: (-x['rating'], -x['similarity']))[:5]

def recommend_remedies(input_symptoms: str, threshold: float):
    input_vector = remedy_vectorizer.transform([input_symptoms])
    scores = cosine_similarity(input_vector, remedy_matrix).flatten()

    results = []
    for idx, score in enumerate(scores):
        if score >= threshold:
            results.append({
                "disease": remedy_data.iloc[idx]["Disease"],
                "remedy": remedy_data.iloc[idx]["Remedy"],
                "similarity": round(score, 2)
            })
    return sorted(results, key=lambda x: -x['similarity'])[:1]


@app.post("/recommend_medicine/", response_model=RecommendationResponse)
async def recommend_medicine(request: RecommendationRequest):
    medicines = recommend_medicines(request.input_symptoms, request.threshold)
    return RecommendationResponse(input_symptoms=request.input_symptoms, recommended_medicines=medicines)

@app.post("/recommend_remedy/", response_model=RecommendationResponse)
async def recommend_remedy(request: RecommendationRequest):
    remedies = recommend_remedies(request.input_symptoms, request.threshold)
    return RecommendationResponse(input_symptoms=request.input_symptoms, recommended_remedies=remedies)

@app.get("/symptom_keywords/")
async def get_symptom_keywords():
    symptoms_list = medicine_data["Symptoms"].dropna().unique().tolist()
    # Remove duplicates and sort
    unique_symptoms = sorted(set(symptoms_list))
    return unique_symptoms

@app.get("/ayurvedic_disease_keywords/")
async def get_ayurvedic_disease_keywords():
    disease_list = remedy_data["Disease"].dropna().unique().tolist()
    # Remove duplicates and sort
    unique_diseases = sorted(set(disease_list))
    return unique_diseases


@app.get("/")
async def root():
    return {"message": "Medicine and Remedy Recommendation API is running!"}

