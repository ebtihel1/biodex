import pandas as pd
from sklearn.linear_model import LinearRegression
import joblib
import numpy as np

class CollectionAIService:
    def __init__(self):
        self.model_path = "model.pkl"
        self.model = None

    def train_model(self, data):
        """
        data = liste de dicts contenant :
        - 'day': nombre de jours écoulés
        - 'volume': volume collecté à cette date
        """
        df = pd.DataFrame(data)
        if len(df) < 2:
            raise ValueError("Pas assez de données pour entraîner le modèle.")

        X = df[['day']]
        y = df['volume']

        model = LinearRegression()
        model.fit(X, y)

        joblib.dump(model, self.model_path)
        self.model = model

        print("✅ Modèle IA pour points de collecte entraîné.")
        return {"coef": model.coef_.tolist(), "intercept": model.intercept_}

    def predict_volume(self, current_day):
        """
        Prédit le volume à une date future (ex: jour+1)
        """
        if self.model is None:
            self.model = joblib.load(self.model_path)

        future_day = np.array([[current_day]])
        prediction = self.model.predict(future_day)
        return float(prediction[0])
