import os
import keras
import keras.utils as image
from keras.applications.imagenet_utils import preprocess_input
import numpy as np
from scipy.spatial import distance
import pickle
import random
from sklearn.decomposition import PCA
from PIL import Image
from io import BytesIO
from keras.models import Model
class Search:
    def __init__(self):
        #Load hình ảnh
        self.dataPLK = './data.pkl'
        self.images_path = r'C:\Users\TAI\OneDrive\Programing\Web\InteriorShop\storage\app\public\images\products'
        image_extensions = ['.jpg', '.png', '.jpeg']   # case-insensitive (upper/lower doesn't matter)
        max_num_images = 10000

        self.images = [os.path.join(dp, f) for dp, dn, filenames in os.walk(self.images_path) for f in filenames if os.path.splitext(f)[1].lower() in image_extensions]
        if max_num_images < len(self.images):
            self.images = [self.images[i] for i in sorted(random.sample(range(len(self.images)), max_num_images))]


        self.model = keras.applications.VGG16(weights='imagenet', include_top=True)
        self.feat_extractor = Model(inputs=self.model.input, outputs=self.model.get_layer("fc2").output)

        # Mở file 'data.pkl' để đọc dữ liệu
        with open(self.dataPLK, 'rb') as file:
            loaded_data = pickle.load(file)
        self.images, self.pca_features, self.pca, self.features_dict = loaded_data


        # features = np.array(features)
        features_matrix = np.array([value['feature'] for value in self.features_dict.values()])

        # pca = PCA(n_components=300)
        self.pca = PCA(n_components=134)
        self.pca.fit(features_matrix)
    def search(self,image_file):
        # Lưu hình ảnh tải lên thành tệp img_user_uploaded.jpg
        urlImg = 'img_user_uploaded.jpg'
        img = Image.open(BytesIO(image_file.read()))
        img.save(os.path.join(os.getcwd(), urlImg))

        # load image and extract features
        new_image, x = self.load_image(urlImg)
        new_features = self.feat_extractor.predict(x)
        new_pca_features = self.pca.transform(new_features)[0]
        distances = [ distance.cosine(new_pca_features, feat) for feat in self.pca_features ]
        idx_closest, folder_names_closest = self.get_closest_images(urlImg, 10)

        return {"folders": folder_names_closest}
    def load_image(self,path):
        img = image.load_img(path, target_size=self.model.input_shape[1:3])
        x = image.img_to_array(img)
        x = np.expand_dims(x, axis=0)
        x = preprocess_input(x)
        return img, x
    def loadToAddNewImages(self):
        image_extensions = ['.jpg', '.png', '.jpeg']
        images = [os.path.join(dp, f) for dp, dn, filenames in os.walk(self.images_path) for f in filenames if os.path.splitext(f)[1].lower() in image_extensions]
        for image_path in images:
            if image_path not in self.features_dict:
                folder_name = os.path.split(os.path.dirname(image_path))[-1]
                img, x = self.load_image(image_path)
                feat = self.feat_extractor.predict(x)[0]
                self.features_dict[image_path] = {'feature': feat, 'folder_name': folder_name}
        # Lưu lại data:
        pickle.dump([images, self.pca_features, self.pca, self.features_dict], open(self.dataPLK, 'wb'))
        return {"status": 200}
    def get_closest_images(self,query_image_path, num_results=5):
        # Trích xuất đặc trưng cho hình ảnh truy vấn
        img, x = self.load_image(query_image_path)
        query_feat = self.feat_extractor.predict(x)[0]

        # Tính khoảng cách giữa hình ảnh truy vấn và tất cả hình ảnh khác
        distances = [distance.cosine(query_feat, self.features_dict[image_path]['feature']) for image_path in self.images]

        # Lấy chỉ mục của các hình ảnh có khoảng cách gần nhất
        idx_closest = sorted(range(len(distances)), key=lambda k: distances[k])[:num_results]

        # Truy cập tên thư mục dựa trên chỉ mục, nhưng chỉ lấy giá trị duy nhất
        closest_folder_names = set([self.features_dict[self.images[i]]['folder_name'] for i in idx_closest])

        return idx_closest, list(closest_folder_names)

    def get_concatenated_images(self,indexes, thumb_height):
        thumbs = []
        for idx in indexes:
            img = image.load_img(self.images[idx])
            img = img.resize((int(img.width * thumb_height / img.height), thumb_height))
            thumbs.append(img)
        concat_image = np.concatenate([np.asarray(t) for t in thumbs], axis=1)
        return concat_image



