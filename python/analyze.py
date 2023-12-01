from pyvi import ViTokenizer
import os
from string import punctuation
import pickle
class Analyze:
    def __init__(self):
        self.rootFolder = os.path.dirname(os.path.abspath(__file__))
        self.stop_word = []
        modelPath = os.path.join(self.rootFolder, 'models/analyze/svm_model.pkl')
        vectorizerPath = os.path.join(self.rootFolder, 'models/analyze/vectorizer.pkl')
        with open(os.path.join(self.rootFolder, 'models/stop_word.txt'),encoding="utf-8") as f :
            text = f.read()
            for word in text.split() :
                self.stop_word.append(word)
            f.close()
        punc = list(punctuation)
        self.stop_word = self.stop_word + punc

        #Load lại mô hình
            # Đọc mô hình từ file
        with open(modelPath, 'rb') as model_file:
            self.model = pickle.load(model_file)

            # Đọc vectorizer nếu cần thiết
        with open(vectorizerPath, 'rb') as vectorizer_file:
            self.vectorizer = pickle.load(vectorizer_file)


    def pre_data_progress(self, text):
        # Đưa chuỗi về chữ thường và trim
        text_lower = text.lower().strip()
        # Tokenize
        text_token = ViTokenizer.tokenize(text_lower)
        # Loại bỏ stop_word và punctuation
        sentence = ""
        for word in text_token.split(" "):
            if (word not in self.stop_word):
                if ("_" in word) or (word.isalpha() == True) or word.isdigit():
                    sentence += word + " "
        return sentence.strip()
    def predict(self, comment):
        comment = self.pre_data_progress(comment)
        new_text_vectorized = self.vectorizer.transform([comment])
        prediction = self.model.predict(new_text_vectorized)
        result = 'Không rõ'
        if prediction[0] == 1:
            result = 'Khen'
        else:
            result = 'Chê'
        return {'comment': comment, 'result': result ,'label': int(prediction[0])}

    