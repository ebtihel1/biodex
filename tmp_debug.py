import io, json
import numpy as np
import onnxruntime as ort
from PIL import Image

session = ort.InferenceSession('ai-service/models/mobilenetv2-7.onnx', providers=['CPUExecutionProvider'])
with open('ai-service/models/imagenet_class_index.json', encoding='utf-8') as fh:
    index = json.load(fh)
labels = [index[str(i)][1] for i in range(len(index))]

for name in ['tmp_bottle.jpg', 'tmp_paper.jpg', 'tmp_can.jpg', 'tmp_leaves.jpg']:
    image = Image.open(name).convert('RGB').resize((224, 224), Image.BILINEAR)
    x = (np.asarray(image, dtype=np.float32) / 127.5) - 1.0
    x = np.expand_dims(x, 0)
    x = np.transpose(x, (0, 3, 1, 2))
    probs = session.run(None, {'data': x})[0][0]
    tops = np.argsort(probs)[-5:][::-1]
    print(f'[{name}]', ' | '.join(f'{labels[int(i)]}={probs[i]:.4f}' for i in tops))