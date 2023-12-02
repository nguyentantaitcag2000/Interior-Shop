import pickle
import os

import pandas as pd
import difflib
from pyvi import ViTokenizer
from string import punctuation
class Recommend:
    def __init__(self):
        self.rootFolder = os.path.dirname(os.path.abspath(__file__))
        # Load stopword
        self.stop_word = []
        with open(os.path.join(self.rootFolder, 'models/stop_word.txt'),encoding="utf-8") as f :
            text = f.read()
            for word in text.split() :
                self.stop_word.append(word)
            f.close()
        punc = list(punctuation)
        self.stop_word = self.stop_word + punc
        # Load mô hình
        with open(os.path.join(self.rootFolder, 'models/recommend/vectorizer_model.pkl'), 'rb') as f:
            self.vectorizer = pickle.load(f)

        with open(os.path.join(self.rootFolder, 'models/recommend/similarity_matrix.pkl'), 'rb') as f:
            self.similarity = pickle.load(f)

        with open(os.path.join(self.rootFolder, 'models/recommend/list_of_all_titles.pkl'), 'rb') as f:
            self.list_of_all_titles = pickle.load(f)

        # Load DataFrame
        self.products_data = pd.read_pickle(os.path.join(self.rootFolder, 'models/recommend/products_data.pkl'))

        self.list_of_all_titles = self.products_data['name_product'].tolist()
    def search(self, name_product):
        
        name_product = self.pre_data_progress(name_product)

        find_close_match = difflib.get_close_matches(name_product, self.list_of_all_titles)

        if find_close_match == []:
            return []
        else:
            close_match = find_close_match[0]

            index_of_the_movie = self.products_data[self.products_data.name_product == close_match]['index'].values[0]

            similarity_score = list(enumerate(self.similarity[index_of_the_movie]))

            sorted_similar_movies = sorted(similarity_score, key = lambda x:x[1], reverse = True)

            results = []
            for i, movie in enumerate(sorted_similar_movies[:30], start=1):
                index = movie[0]
                title_from_index = self.products_data.at[index, 'name_product']
                id_product = self.products_data.at[index, 'id_product'].item()  # Sử dụng item() để chuyển đổi numpy.int64

                results.append({"ID_Product": id_product, "Name_Product": title_from_index.replace('_',' ')})

            return results
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