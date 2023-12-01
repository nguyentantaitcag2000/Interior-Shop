import pickle
import os

import pandas as pd
import difflib
class Recommend:
    def __init__(self):
        self.rootFolder = os.path.dirname(os.path.abspath(__file__))
    
        # Load mô hình
        with open(os.path.join(self.rootFolder, 'models/recommend/vectorizer_model.pkl'), 'rb') as f:
            self.vectorizer = pickle.load(f)

        with open(os.path.join(self.rootFolder, 'models/recommend/similarity_matrix.pkl'), 'rb') as f:
            self.similarity = pickle.load(f)

        with open(os.path.join(self.rootFolder, 'models/recommend/list_of_all_titles.pkl'), 'rb') as f:
            self.list_of_all_titles = pickle.load(f)

        # Load DataFrame
        self.products_data = pd.read_pickle(os.path.join(self.rootFolder, 'models/recommend/products_data.pkl'))
    def search(self, name_product):
        list_of_all_titles = self.products_data['name_product'].tolist()

        find_close_match = difflib.get_close_matches(name_product, list_of_all_titles)

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

                results.append({"ID_Product": id_product, "Name_Product": title_from_index})

            return results