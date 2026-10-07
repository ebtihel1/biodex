import io
from PIL import Image, ImageDraw, ImageFilter

SIZE = 320


def bottle_image():
    img = Image.new('RGB', (SIZE, SIZE), (235, 230, 225))
    d = ImageDraw.Draw(img)
    # Corps de la bouteille : cylindre blanc translucide
    left, top, right, bottom = 118, 70, 210, 270
    d.rounded_rectangle([left, top, right, bottom], radius=22, fill=(238, 245, 248), outline=(180, 195, 200), width=2)
    # Bouchon bleu
    d.rectangle([132, 34, 196, 70], fill=(35, 120, 200), outline=(20, 90, 160))
    d.rectangle([132, 34, 196, 44], fill=(60, 140, 220))
    # Étiquette bleue
    d.rectangle([118, 150, 210, 210], fill=(50, 130, 215), outline=(35, 110, 190))
    d.text((140, 170), 'EAU', fill=(255, 255, 255))
    d.text((140, 188), 'BIO', fill=(255, 255, 255))
    # Reflet spéculaire vertical (brillance plastique)
    d.rectangle([132, 80, 138, 240], fill=(245, 250, 252))
    d.line([130, 78, 130, 242], fill=(228, 238, 242), width=1)
    # Ombres de base
    d.ellipse([112, 272, 216, 282], fill=(200, 196, 190))
    return img


def paper_image():
    img = Image.new('RGB', (SIZE, SIZE), (200, 198, 192))
    d = ImageDraw.Draw(img)
    d.rectangle([40, 60, 280, 280], fill=(246, 244, 238), outline=(205, 200, 190), width=2)
    for i in range(6):
        y = 90 + i * 30
        d.line([60, y, 260, y], fill=(215, 210, 200), width=2)
    return img


def metal_can_image():
    img = Image.new('RGB', (SIZE, SIZE), (225, 222, 218))
    d = ImageDraw.Draw(img)
    d.rounded_rectangle([128, 60, 196, 270], radius=10, fill=(200, 202, 205), outline=(150, 155, 160))
    d.ellipse([128, 96, 196, 140], fill=(210, 213, 216), outline=(150, 155, 160))
    d.text((138, 150), 'COLA', fill=(90, 90, 95))
    return img


def green_leaves_image():
    img = Image.new('RGB', (SIZE, SIZE), (225, 223, 210))
    d = ImageDraw.Draw(img)
    d.ellipse([80, 80, 200, 200], fill=(70, 150, 60), outline=(45, 120, 40))
    d.ellipse([140, 130, 250, 235], fill=(85, 160, 70), outline=(45, 120, 40))
    d.ellipse([100, 160, 190, 260], fill=(95, 165, 80))
    return img


def save(img, name):
    buf = io.BytesIO()
    img.save(buf, format='JPEG', quality=90)
    with open(name, 'wb') as fh:
        fh.write(buf.getvalue())
    return len(buf.getvalue())


print('bottle bytes:', save(bottle_image(), 'tmp_bottle.jpg'))
print('paper bytes:', save(paper_image(), 'tmp_paper.jpg'))
print('can bytes:', save(metal_can_image(), 'tmp_can.jpg'))
print('leaves bytes:', save(green_leaves_image(), 'tmp_leaves.jpg'))